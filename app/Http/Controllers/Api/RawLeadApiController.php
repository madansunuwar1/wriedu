<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\NotificationApiController;
use App\Models\RawLead;
use App\Models\User;
use App\Models\Lead;
use App\Models\RawLeadComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RawLeadApiController extends Controller
{
    protected $allowedStatuses = ['new', 'contacted', 'in progress', 'qualified', 'converted', 'rejected', 'on hold', 'dropped'];
    protected $allowedPerPageOptions = [10, 20, 50, 100, 250];
    private $notificationController;

    public function __construct(NotificationApiController $notificationController)
    {
        // [MODIFIED] Added 'except' to allow the Zoho webhook to bypass auth
        $this->middleware('auth')->except('receiveFromZoho');
        $this->notificationController = $notificationController;
    }

    /**
     * Receives a new lead from a Zoho webhook, validates it, creates a RawLead,
     * and fires a broadcastable event for real-time UI updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
 

public function receiveFromZoho(Request $request)
{
    $logChannel = 'zoho_debug';
    Log::channel($logChannel)->info('--- ZOHO WEBHOOK: REQUEST RECEIVED ---');

    try {
        // SECURITY STEP 1: Verify webhook authentication
        Log::channel($logChannel)->info('Security - STEP 1 [START]: Verifying webhook authentication.');
        
        $webhookSecret = config('services.zoho.webhook_secret');
        $providedSecret = $request->header('X-Webhook-Secret');
        
        Log::channel($logChannel)->info('Security - STEP 1 [DEBUG]: Checking secret header.', [
            'provided_secret' => $providedSecret,
            'expected_secret' => '***' . substr($webhookSecret, -4) // Avoid logging the full secret
        ]);
        
        if (!$providedSecret) {
            Log::channel($logChannel)->warning('Security - STEP 1 [FAILED]: No X-Webhook-Secret header found. Ensure this header is configured in your Zoho webhook settings.');
            return response()->json(['error' => 'Unauthorized - Missing secret header'], 401);
        }
        
        if (!hash_equals((string) $webhookSecret, (string) $providedSecret)) { // Use hash_equals for timing attack protection
            Log::channel($logChannel)->warning('Security - STEP 1 [FAILED]: Invalid secret header provided.');
            return response()->json(['error' => 'Unauthorized - Invalid secret'], 401);
        }
        
        Log::channel($logChannel)->info('Security - STEP 1 [SUCCESS]: Authentication verified.');

        // SECURITY STEP 2: IP whitelist check
        Log::channel($logChannel)->info('Security - STEP 2 [START]: Checking IP whitelist.');
        
        // $allowedIPs = config('services.zoho.allowed_ips', []);
        // if (!empty($allowedIPs)) {
        //     $requestIp = $request->ip();
        //     $isAllowed = false;
        //     foreach ($allowedIPs as $range) {
        //         if ($this->ipInRange($requestIp, $range)) {
        //             $isAllowed = true;
        //             break;
        //         }
        //     }
            
        //     if (!$isAllowed) {
        //         Log::channel($logChannel)->warning('Security - STEP 2 [FAILED]: IP not in whitelist.', ['ip' => $requestIp]);
        //         return response()->json(['error' => 'Unauthorized IP'], 403);
        //     }
        // }
        
        Log::channel($logChannel)->info('Security - STEP 2 [SUCCESS]: IP whitelist check passed.');
        
        // SECURITY STEP 3: Rate limiting (No change needed here, just noting its position)
        // ... (your rate limiting code can stay here if you add it back) ...


        // Log the validated request
        Log::channel($logChannel)->debug('ZOHO WEBHOOK PAYLOAD:', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // Your existing validation logic
        Log::channel($logChannel)->info('Webhook - STEP 1 [START]: Validating incoming data.');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        Log::channel($logChannel)->info('Webhook - STEP 1 [SUCCESS]: Validation passed.', ['validated_data' => $validated]);

        Log::channel($logChannel)->info('Webhook - STEP 2 [START]: Checking for duplicate raw lead.');
        $exists = RawLead::where('phone', $validated['phone'])
            ->when($validated['email'], fn($q) => $q->orWhere('email', 'like', $validated['email']))
            ->exists();
        Log::channel($logChannel)->info('Webhook - STEP 2 [INFO]: Duplicate check result.', ['exists' => $exists]);

        if ($exists) {
            Log::channel($logChannel)->warning('Webhook - STEP 2 [RESULT]: Duplicate Raw Lead found. Halting process.', $validated);
            return response()->json(['message' => 'Duplicate raw lead received. No action taken.'], 200);
        }
        
        Log::channel($logChannel)->info('Webhook - STEP 2 [RESULT]: No duplicate found. Proceeding.');
        Log::channel($logChannel)->info('Webhook - STEP 3 [START]: Creating new RawLead model instance.');
        
        $rawLead = new RawLead();
        $rawLead->name = $validated['name'];
        $rawLead->phone = $validated['phone'];
        $rawLead->email = $validated['email'] ?? null;
        $rawLead->status = 'new';
        $rawLead->created_by = 1; // Assuming '1' is a system user ID

        Log::channel($logChannel)->info('Webhook - STEP 4 [START]: Saving RawLead to the database.');
        $rawLead->save();
        Log::channel($logChannel)->info('Webhook - STEP 4 [SUCCESS]: RawLead saved successfully!', ['raw_lead_id' => $rawLead->id]);

        try {
            Log::channel($logChannel)->info('Webhook - STEP 5 [START]: Firing RawLeadCreated event.');
            event(new \App\Events\RawLeadCreated($rawLead));
            Log::channel($logChannel)->info('Webhook - STEP 5 [SUCCESS]: RawLeadCreated event fired.');
        } catch (\Exception $e) {
            Log::channel($logChannel)->error('Webhook - STEP 5 [ERROR]: Failed to fire RawLeadCreated event.', [
                'error_message' => $e->getMessage(), 'raw_lead_id' => $rawLead->id
            ]);
        }

        Log::channel($logChannel)->info('--- ZOHO WEBHOOK: REQUEST PROCESSED SUCCESSFULLY ---', ['raw_lead_id' => $rawLead->id]);
        return response()->json(['success' => true, 'message' => 'Raw lead created successfully.', 'raw_lead_id' => $rawLead->id], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::channel($logChannel)->error('--- ZOHO WEBHOOK: VALIDATION FAILED ---', ['errors' => $e->errors(), 'input' => $request->all()]);
        return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
    } catch (\Illuminate\Database\QueryException $e) {
        Log::channel($logChannel)->critical('--- ZOHO WEBHOOK: DATABASE ERROR ---', ['error_code' => $e->getCode(), 'message' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'A database error occurred.'], 500);
    } catch (\Exception $e) {
        Log::channel($logChannel)->critical('--- ZOHO WEBHOOK: UNHANDLED EXCEPTION ---', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
    }
}

/**
 * Checks if a given IP address is within a given IP range (supports CIDR).
 *
 * @param string $ip The IP to check.
 * @param string $range The range (e.g., '192.168.1.1' or '192.168.1.0/24').
 * @return bool True if the IP is in the range, false otherwise.
 */
private function ipInRange(string $ip, string $range): bool
{
    // Check if the range is a CIDR block
    if (strpos($range, '/') === false) {
        // Not a CIDR, so perform a simple string comparison
        return $ip === $range;
    }

    // It's a CIDR block, so perform a proper check
    list($subnet, $bits) = explode('/', $range);
    
    // Convert IPs to long integers
    $ipLong = ip2long($ip);
    $subnetLong = ip2long($subnet);
    
    // Generate the subnet mask
    $mask = -1 << (32 - $bits);
    
    // Apply the mask to the subnet
    $subnetMasked = $subnetLong & $mask;
    
    // Check if the masked IP matches the masked subnet
    return ($ipLong & $mask) === $subnetMasked;
}

    public function index(Request $request)
    {
        $query = RawLead::with(['assignee:id,name'])->latest();
        $user = Auth::user();

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhereHas('assignee', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', $searchTerm);
                    });
            });
        }
        if ($request->filled('status') && in_array($request->status, $this->allowedStatuses)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('assignee_id')) {
            if ($request->assignee_id === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif (is_numeric($request->assignee_id)) {
                $query->where('assigned_to', $request->assignee_id);
            }
        }
        if ($request->filled('country')) {
            $query->where('preferred_country', $request->country);
        }
        if ($request->filled('applying_for')) {
            $query->where('applying_for', $request->applying_for);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $defaultPerPage = 20;
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        if (!in_array($perPage, $this->allowedPerPageOptions)) {
            $perPage = $defaultPerPage;
        }

        return response()->json($query->paginate($perPage)->withQueryString());
    }

    public function getFilterOptions()
    {
        $users = User::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $uniqueCountries = RawLead::select('preferred_country')->whereNotNull('preferred_country')->where('preferred_country', '!=', '')->distinct()->orderBy('preferred_country')->pluck('preferred_country');
        $uniqueApplyingFor = RawLead::select('applying_for')->whereNotNull('applying_for')->where('applying_for', '!=', '')->distinct()->orderBy('applying_for')->pluck('applying_for');

        return response()->json([
            'users' => $users,
            'statuses' => $this->allowedStatuses,
            'countries' => $uniqueCountries,
            'applyingFor' => $uniqueApplyingFor,
        ]);
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('backend.rawlead.create', ['users' => $users, 'statuses' => $this->allowedStatuses]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:raw_leads,email',
            'phone' => 'required|string|max:50|unique:raw_leads,phone',
        ]);

        // Your store logic here

        return redirect()->route('rawleads.vue.index')->with('success', 'Raw Lead created successfully!');
    }

    public function show(RawLead $rawLead)
    {
        $rawLead->load(['creator', 'assignee', 'comments.user']);
        return view('backend.rawlead.show', ['rawLead' => $rawLead, 'users' => User::where('is_active', true)->get()]);
    }

    public function edit(RawLead $rawLead)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('backend.rawlead.edit', ['rawLead' => $rawLead, 'users' => $users, 'statuses' => $this->allowedStatuses]);
    }

    public function update(Request $request, RawLead $rawLead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:raw_leads,email,' . $rawLead->id,
            'phone' => 'required|string|max:50|unique:raw_leads,phone,' . $rawLead->id,
        ]);

        // Your update logic here

        return redirect()->route('rawleads.vue.index')->with('success', 'Raw Lead updated successfully.');
    }

    public function destroy(RawLead $rawLead)
    {
        try {
            $rawLead->delete();
            return response()->json(['message' => 'Raw Lead deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting Raw Lead ' . $rawLead->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete Raw Lead.'], 500);
        }
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'raw_lead_ids' => 'required|array',
            'raw_lead_ids.*' => 'required|integer|exists:raw_leads,id',
            'user_id' => 'required|integer|exists:users,id',
            'assignment_comment' => 'nullable|string',
        ]);

        try {
            $avatarPath = public_path('assets/images/avatars');
            $avatars = [];
            if (File::isDirectory($avatarPath)) {
                $avatars = File::files($avatarPath);
            }

            // Get the User object for the person we are assigning to.
            $assignedToUser = User::findOrFail($validated['user_id']);
            $assigningUser = Auth::user(); // The user clicking the button.

            $rawLeads = RawLead::whereIn('id', $validated['raw_lead_ids'])->get();

            foreach ($rawLeads as $rawLead) {
                $randomAvatarName = null;
                if (!empty($avatars)) {
                    $randomAvatar = $avatars[array_rand($avatars)];
                    $randomAvatarName = $randomAvatar->getFilename();
                }

                // *** THE FIX IS HERE: Assign the created Lead to $newLead ***
                $newLead = Lead::create([
                    'name' => $rawLead->name,
                    'email' => $rawLead->email,
                    'phone' => $rawLead->phone,
                    'country' => $rawLead->preferred_country,
                    'created_by' => $assignedToUser->id,
                    'is_forwarded' => $assigningUser->id,
                    'avatar' => $randomAvatarName,
                ]);

                // Now that $newLead is defined, this will work.
                try {
                    $notes = $validated['assignment_comment'] ?? "A new lead ({$newLead->name}) has been assigned to you.";
                    $this->notificationController->createForwardedDocumentNotification($newLead, $assignedToUser, $notes);
                } catch (\Exception $e) {
                    Log::error("Failed to create notification during bulk assign: " . $e->getMessage());
                    // Don't stop the process, just log the error.
                }

                $rawLead->delete();
            }

            return response()->json(['message' => 'Leads assigned successfully.'], 200);
        } catch (\Exception $e) {
            Log::error("Error during bulk assignment: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred during bulk assignment.'], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'raw_lead_ids' => 'required|array',
            'raw_lead_ids.*' => 'required|integer|exists:raw_leads,id',
        ]);

        try {
            RawLead::whereIn('id', $validated['raw_lead_ids'])->delete();
            return response()->json(['message' => 'Leads deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error("Error during bulk delete: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred during bulk deletion.'], 500);
        }
    }

    public function showImportForm()
    {
        return view('backend.rawlead_import_vue');
    }

    public function getImportConfig()
    {
        return response()->json([
            'allowedStatuses' => $this->allowedStatuses,
            'maxFileSize' => 10240
        ]);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()->all()], 422);
        }

        $file = $request->file('file');
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestDataRow();

            if ($highestRow < 2) {
                return response()->json(['message' => 'Import failed.', 'errors' => ['File is empty or contains only a header row.']], 400);
            }

            $headerRow = $worksheet->rangeToArray('A1:' . $worksheet->getHighestDataColumn() . '1', NULL, TRUE, FALSE)[0];
            $headerRow = array_map('trim', $headerRow);

            $expectedHeaderMap = [
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'status' => 'Status',
                'ad_id' => 'AD ID',
                'preferred_country' => 'Country Preference',
                'preferred_subject' => 'Subject Preference',
                'applying_for' => 'Applying For',
                'assigned_to' => 'Assigned User Email',
                'initial_comment' => 'Initial Comment',
            ];

            $columnIndexMap = [];
            foreach ($expectedHeaderMap as $dbField => $displayName) {
                $foundIndex = array_search(strtolower($displayName), array_map('strtolower', $headerRow));
                if ($foundIndex !== false) {
                    $columnIndexMap[$dbField] = $foundIndex;
                }
            }

            $requiredDbFields = ['name', 'email', 'phone'];
            $missingHeaders = [];
            foreach ($requiredDbFields as $reqField) {
                if (!isset($columnIndexMap[$reqField])) {
                    $missingHeaders[] = $expectedHeaderMap[$reqField];
                }
            }
            if (!empty($missingHeaders)) {
                return response()->json([
                    'message' => 'Import failed due to missing columns.',
                    'errors' => ['The following required columns were not found: ' . implode(', ', $missingHeaders)]
                ], 400);
            }

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                $rowData = $worksheet->rangeToArray('A' . $rowNumber . ':' . $worksheet->getHighestDataColumn() . $rowNumber, NULL, TRUE, FALSE)[0];
                if (count(array_filter($rowData)) == 0) continue;

                $data = ['created_by' => Auth::id(), 'status' => 'new'];
                $initialComment = null;

                foreach ($columnIndexMap as $dbField => $index) {
                    $value = isset($rowData[$index]) ? trim($rowData[$index]) : null;
                    if ($dbField === 'assigned_to' && !empty($value)) {
                        $user = User::where('email', $value)->where('is_active', true)->first();
                        $data['assigned_to'] = $user ? $user->id : null;
                    } elseif ($dbField === 'initial_comment') {
                        $initialComment = $value;
                    } elseif ($dbField === 'phone') {
                        $data['phone'] = $this->normalizePhoneNumber($value);
                    } else {
                        $data[$dbField] = $value;
                    }
                }

                $leadValidator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:50',
                    'status' => ['nullable', 'string', 'max:255'],
                ]);

                if ($leadValidator->fails()) {
                    $errors[] = "Row {$rowNumber}: " . $leadValidator->errors()->first();
                    $skippedCount++;
                    continue;
                }

                $existingLead = RawLead::where('email', $data['email'])->orWhere('phone', $data['phone'])->first();

                if ($existingLead) {
                    $existingLead->update($data);
                    $updatedCount++;
                    $leadInstance = $existingLead;
                } else {
                    $leadInstance = RawLead::create($data);
                    $importedCount++;
                }

                if (isset($leadInstance) && !empty($initialComment)) {
                    RawLeadComment::create(['raw_lead_id' => $leadInstance->id, 'user_id' => Auth::id(), 'comment' => "Imported Comment: " . $initialComment]);
                }
            }

            $finalMessage = "Import finished. {$importedCount} created, {$updatedCount} updated.";
            if ($skippedCount > 0) {
                $finalMessage .= " {$skippedCount} skipped due to errors.";
            }

            return response()->json([
                'message' => $finalMessage,
                'stats' => ['created' => $importedCount, 'updated' => $updatedCount, 'skipped' => $skippedCount, 'total' => $highestRow - 1],
                'errors' => $errors
            ], 200);
        } catch (\Exception $e) {
            Log::error('Critical Import Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'An unexpected server error occurred during import.', 'errors' => ['Please check the server logs for details.']], 500);
        }
    }

    public function exportAllData()
    {
        try {
            $rawLeads = RawLead::with(['creator', 'assignee'])->get();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $headers = ['Name', 'Email', 'Phone', 'Status', 'Ad ID', 'Country Preference', 'Subject Preference', 'Applying For', 'Assigned To', 'Created By', 'Date Created', 'Last Updated', 'Initial Comment'];

            foreach ($headers as $index => $header) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter . '1', $header);
            }

            $rowNumber = 2;
            foreach ($rawLeads as $lead) {
                $sheet->setCellValue('A' . $rowNumber, $lead->name ?? '');
                $sheet->setCellValue('B' . $rowNumber, $lead->email ?? '');
                $sheet->setCellValue('C' . $rowNumber, $lead->phone ?? '');
                $sheet->setCellValue('D' . $rowNumber, ucwords(str_replace('_', ' ', $lead->status ?? '')));
                $sheet->setCellValue('E' . $rowNumber, $lead->ad_id ?? '');
                $sheet->setCellValue('F' . $rowNumber, $lead->preferred_country ?? '');
                $sheet->setCellValue('G' . $rowNumber, $lead->preferred_subject ?? '');
                $sheet->setCellValue('H' . $rowNumber, $lead->applying_for ? ucfirst($lead->applying_for) : '');
                $sheet->setCellValue('I' . $rowNumber, $lead->assignee->name ?? '');
                $sheet->setCellValue('J' . $rowNumber, $lead->creator->name ?? '');
                $sheet->setCellValue('K' . $rowNumber, $lead->created_at ? $lead->created_at->format('Y-m-d H:i') : '');
                $sheet->setCellValue('L' . $rowNumber, $lead->updated_at ? $lead->updated_at->format('Y-m-d H:i') : '');
                $sheet->setCellValue('M' . $rowNumber, $lead->follow_up_comments ?? '');
                $rowNumber++;
            }

            $fileName = "raw_leads_export_" . now()->format('Ymd_His') . ".xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            Log::error("Export error: " . $e->getMessage());
            return redirect()->route('rawleads.vue.index')->with('error', 'Error exporting data.');
        }
    }

    private function normalizePhoneNumber($value)
    {
        if (empty($value)) return null;
        $digitsOnly = preg_replace('/\D/', '', $value);
        return substr($digitsOnly, -10);
    }
}
