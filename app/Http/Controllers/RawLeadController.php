<?php

namespace App\Http\Controllers;

use App\Models\RawLead;
use App\Models\User;
use App\Models\Lead;  // Import Lead Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;
use App\Models\RawLeadComment;

class RawLeadController extends Controller
{
    protected $allowedStatuses = ['new', 'contacted', 'in progress', 'qualified', 'converted', 'rejected', 'on hold', 'dropped'];
    protected $allowedPerPageOptions = [10, 20, 50, 100, 250];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = RawLead::with(['creator', 'assignee'])->latest();
        $user = Auth::user();

        if (!$user->hasRole('Admin') && !$user->can('view_all_raw_leads')) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhereHas('creator', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('assignee', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', $searchTerm);
                    });
            });
        }

        if ($request->filled('status')) {
            if (in_array($request->status, $this->allowedStatuses)) {
                $query->where('status', $request->status);
            }
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

        $defaultPerPage = 10;
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        if (!in_array($perPage, $this->allowedPerPageOptions)) {
            $perPage = $defaultPerPage;
        }

        $rawLeads = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return $this->getAjaxResponse($rawLeads);
        }

        $users = User::where('is_active', true)->orderBy('name')->get();
        $statuses = $this->allowedStatuses;
        $allowedPerPage = $this->allowedPerPageOptions;

        return view('backend.leadform.rawlead', compact(
            'rawLeads',
            'users',
            'statuses',
            'perPage',
            'allowedPerPage'
        ));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $statuses = $this->allowedStatuses;
        $applyingForOptions = ['undergraduate', 'postgraduate', 'diploma', 'foundation', 'other'];
        return view('backend.rawlead.create', compact('users', 'statuses', 'applyingForOptions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:raw_leads,email',
            'phone' => 'required|string|max:50|unique:raw_leads,phone',
            'preferred_country' => 'nullable|string|max:100',
            'preferred_subject' => 'nullable|string|max:100',
            'applying_for' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => ['required', 'string', Rule::in($this->allowedStatuses)],
            'follow_up_comments' => 'nullable|string|max:2000',
        ]);

        $validatedData['created_by'] = Auth::id();
        $validatedData['phone'] = $this->normalizePhoneNumber($validatedData['phone']);

        try {
            $rawLead = RawLead::create($validatedData);

            return redirect()->route('backend.leadform.rawlead')
                ->with('success', 'Raw Lead created successfully!');
        } catch (\Exception $e) {
            Log::error('Error storing Raw Lead: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create Raw Lead. ' . $e->getMessage());
        }
    }

    public function show(RawLead $rawLead)
    {
        $rawLead->load(['creator', 'assignee']);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $statuses = $this->allowedStatuses;
        return view('backend.rawlead.show', compact('rawLead', 'users', 'statuses'));
    }

    public function edit(Request $request, RawLead $rawLead)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $statuses = $this->allowedStatuses;
        $applyingForOptions = ['undergraduate', 'postgraduate', 'diploma', 'foundation', 'other'];
        return view('backend.rawlead.edit', compact('rawLead', 'users', 'statuses', 'applyingForOptions'));
    }

    public function update(Request $request, RawLead $rawLead)
    {
        $validatedData = $request->validate([
            'ad_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:raw_leads,email,' . $rawLead->id,
            'phone' => 'required|string|max:50|unique:raw_leads,phone,' . $rawLead->id,
            'preferred_country' => 'nullable|string|max:100',
            'preferred_subject' => 'nullable|string|max:100',
            'applying_for' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => ['required', 'string', Rule::in($this->allowedStatuses)],
        ]);

        $validatedData['phone'] = $this->normalizePhoneNumber($validatedData['phone']);

        try {
            $rawLead->update($validatedData);

            return redirect()->route('backend.leadform.rawlead')
                ->with('success', 'Raw Lead updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating Raw Lead ' . $rawLead->id . ': ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update Raw Lead. ' . $e->getMessage());
        }
    }

    public function destroy(RawLead $rawLead)
    {
        try {
            $rawLead->delete();
            return redirect()->route('backend.leadform.rawlead')
                ->with('success', 'Raw Lead deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting Raw Lead ' . $rawLead->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete Raw Lead. ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, RawLead $rawLead)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($this->allowedStatuses)],
        ]);

        try {
            $rawLead->update(['status' => $validated['status']]);

            return redirect()->back()->with('success', 'Raw Lead status updated successfully.');
        } catch (\Exception $e) {
            Log::error("Error updating status for Raw Lead {$rawLead->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status. ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'raw_lead_ids' => 'required|array',
            'raw_lead_ids.*' => 'required|integer|exists:raw_leads,id',
            'user_id' => 'required|integer|exists:users,id',
            'assignment_comment' => 'nullable|string|max:1000'
        ]);

        $leadIds = $validated['raw_lead_ids'];
        $assigneeId = $validated['user_id'];

        try {
            $rawLeads = RawLead::whereIn('id', $leadIds)->get();

            foreach ($rawLeads as $rawLead) {
                $lead = new Lead();
                $lead->name = $rawLead->name;
                $lead->email = $rawLead->email;
                $lead->phone = $rawLead->phone;
                $lead->country = $rawLead->preferred_country;

                // Store the assignee ID in created_by (or whatever field you prefer)
                $lead->created_by = $assigneeId;

                // If you still want to track who forwarded it
                $lead->is_forwarded = Auth::id(); // The current user who is doing the forwarding
                $lead->assignRandomAvatar();
                // Map other fields as needed
                $lead->save();

                $rawLead->delete();
            }

            return redirect()->route('backend.leadform.rawlead')->with('success', 'Raw Leads assigned and converted successfully.');
        } catch (\Exception $e) {
            Log::error("Error during bulk assignment: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error during bulk assignment: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'raw_lead_ids' => 'required|array',
            'raw_lead_ids.*' => 'required|integer|exists:raw_leads,id',
        ]);

        $leadIds = $validated['raw_lead_ids'];

        try {
            RawLead::whereIn('id', $leadIds)->delete();

            return redirect()->route('backend.leadform.rawlead')->with('success', 'Raw Leads deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error during bulk delete: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error during bulk deletion: ' . $e->getMessage());
        }
    }

    public function upload()
    {
        $allowedStatuses = $this->allowedStatuses;
        return view('backend.leadform.import', compact('allowedStatuses'));
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240',]);
        $file = $request->file('file');
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestDataRow();
            if ($highestRow < 1) {
                return redirect()->back()->with('error', 'Import failed. File is empty.');
            }
            if ($highestRow >= 1) {
                $headerRow = $worksheet->rangeToArray('A1:' . $worksheet->getHighestDataColumn() . '1', NULL, TRUE, FALSE)[0];
                \Log::info("Headers read from file:", $headerRow);
            } else {
                return redirect()->back()->with('error', 'Import failed. Cannot read header row.');
            }
            $expectedHeaderMap = ['Student Name' => 'name', 'Email ID' => 'email', 'Contact Number' => 'phone','Address' => 'location', 'status' => 'status', 'Add ID' => 'ad_id', 'Pass Year' => 'pass_year', 'Country' => 'preferred_country', 'Applying Course' => 'preferred_subject', 'Applying Degree' => 'applying_for', 'Assign' => 'assigned_to', 'GPA' => 'pass_degree_gpa', 'English proficency' => 'english_proficiency', 'Englsih Score' => 'english_score', 'Date' => 'twelfth_english_grade','Previous Degree' => 'previous_degree', 'initial comment' => 'initial_comment',];
            $columnIndexMap = [];
            foreach ($expectedHeaderMap as $headerName => $dbField) {
                $index = array_search($headerName, $headerRow);
                if ($index !== false) {
                    $columnIndexMap[$dbField] = $index;
                }
            }
            $requiredHeaders = ['phone'];
            $missingHeaders = [];
            foreach ($requiredHeaders as $reqField) {
                if (!isset($columnIndexMap[$reqField])) {
                    $headerName = array_search($reqField, $expectedHeaderMap);
                    $missingHeaders[] = $headerName ?: $reqField;
                }
            }
            if (!empty($missingHeaders)) {
                return redirect()->back()->with('error', 'Import failed. Missing columns: ' . implode(', ', $missingHeaders));
            }
            if ($highestRow < 2) {
                return redirect()->back()->with('warning', 'Import file only contained header row.');
            }
            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                $rowData = $worksheet->rangeToArray('A' . $rowNumber . ':' . $worksheet->getHighestDataColumn() . $rowNumber, NULL, TRUE, FALSE)[0];
                if (count(array_filter($rowData)) == 0) {
                    continue;
                }
                $data = ['created_by' => Auth::id(), 'ad_id' => null, 'preferred_country' => null, 'preferred_subject' => null, 'applying_for' => null, 'assigned_to' => null, 'status' => 'new',];
                $initialComment = null;
                foreach ($columnIndexMap as $dbField => $index) {
                    $value = isset($rowData[$index]) ? trim($rowData[$index]) : null;
                    if ($dbField === 'assigned_to') {
                        if (!empty($value)) {
                            $user = User::where('email', $value)->where('is_active', true)->first();
                            $data['assigned_to'] = $user ? $user->id : null;
                            if (!$user) {
                                Log::warning("Import Warning (Row {$rowNumber}): User email '{$value}' not found/inactive.");
                            }
                        }
                    } elseif ($dbField === 'initial_comment') {
                        $initialComment = $value;
                    } elseif ($dbField === 'phone') {
                        $data[$dbField] = $this->normalizePhoneNumber($value);
                    } elseif ($dbField === 'status') {
                        if ($value !== '' && $value !== null) {
                            $data['status'] = strtolower($value);
                        }
                    } else {
                        $data[$dbField] = ($value !== '' && $value !== null) ? $value : null;
                    }
                }
                $data['name'] = $data['name'] ?? null;
                $data['email'] = $data['email'] ?? null;
                $validator = Validator::make($data, ['name' => 'nullable|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'required|string|max:50', 'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)], 'ad_id' => 'nullable|string|max:255', 'preferred_country' => 'nullable|string|max:100', 'preferred_subject' => 'nullable|string|max:100', 'applying_for' => 'nullable|string|max:50', 'assigned_to' => 'nullable|exists:users,id', 'created_by' => 'required|exists:users,id',], ['email.required' => "R{$rowNumber}: Email missing.", 'email.email' => "R{$rowNumber}: Invalid email.", 'name.required' => "R{$rowNumber}: Name missing.", 'phone.required' => "R{$rowNumber}: Phone missing.", 'status.in' => "R{$rowNumber}: Invalid status '{$data['status']}'.",]);
                if ($validator->fails() || empty($data['status'])) {
                    if ($validator->errors()->has('status')) {
                        $errors = array_merge($errors, $validator->errors()->all());
                        $skippedCount++;
                        continue;
                    } elseif ($validator->fails()) {
                        $errors = array_merge($errors, $validator->errors()->all());
                        $skippedCount++;
                        continue;
                    }
                    $data['status'] = 'new';
                }
                if (is_null($data['phone'])) {
                    $errors[] = "R{$rowNumber}: Invalid phone.";
                    $skippedCount++;
                    continue;
                }
                $existingLead = RawLead::where('name', $data['name'])
                    ->where('phone', $data['phone'])
                    ->first();

                if ($existingLead) {
                    try {
                        $existingLead->update($data);
                        $updatedCount++;
                        $leadInstance = $existingLead;
                    } catch (\Exception $e) {
                        $errors[] = "R{$rowNumber}: Update error (Name: {$data['name']}, Phone: {$data['phone']}): " . $e->getMessage();
                        $skippedCount++;
                        continue;
                    }
                } else {
                    try {
                        $leadInstance = RawLead::create($data);
                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "R{$rowNumber}: Create error (Name: {$data['name']}, Phone: {$data['phone']}): " . $e->getMessage();
                        $skippedCount++;
                        continue;
                    }
                }
                if (isset($leadInstance) && !empty($initialComment)) {
                    try {
                        RawLeadComment::create(['raw_lead_id' => $leadInstance->id, 'user_id' => Auth::id(), 'comment' => "Imported Comment: " . $initialComment]);
                    } catch (\Exception $e) {
                        $errors[] = "R{$rowNumber}: Comment error: " . $e->getMessage();
                        Log::error("Import Error (R{$rowNumber}) add comment ID {$leadInstance->id}: " . $e->getMessage());
                    }
                }
                unset($leadInstance);
            }
            $message = "Import finished. {$importedCount} created, {$updatedCount} updated.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} skipped.";
            }
            $redirect = redirect()->route('backend.leadform.rawlead');
            if (!empty($errors)) {
                Log::error("Raw Lead Import Errors: ", $errors);
                $maxErrorsToShow = 10;
                $errorSummary = array_slice($errors, 0, $maxErrorsToShow);
                if (count($errors) > $maxErrorsToShow) {
                    $errorSummary[] = "... (" . (count($errors) - $maxErrorsToShow) . " more errors)";
                }
                $redirect->with('import_errors', $errorSummary);
                $redirect->with('warning', $message);
            } else {
                $redirect->with('success', $message);
            }
            return $redirect;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Log::error('Spreadsheet read error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error reading file.');
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Unexpected import error.');
        }
    }

    private function normalizePhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $normalized = preg_replace('/[^\d+]/', '', $phone);
        return $normalized ?: null;
    }

       private function getAjaxResponse($rawLeads)
    {
        $tableHtml = '';
        $defaultAvatarForBlade = asset('assets/images/profile/user-default.jpg');

        if ($rawLeads->isEmpty()) {
            $tableHtml = '<tr><td colspan="10" class="text-center py-4">No raw leads found matching your criteria.</td></tr>';
        } else {
            foreach ($rawLeads as $rawLead) {
                $statusClass = 'status-' . str_replace(' ', '-', strtolower($rawLead->status ?? 'default'));
                $statusText = ucwords(str_replace('_', ' ', $rawLead->status ?? 'Unknown'));
                $assigneeName = $rawLead->assignee->name ?? 'Unassigned';
                $applyingForText = $rawLead->applying_for ? ucfirst($rawLead->applying_for) : '-';
                $createdAt = $rawLead->created_at ? $rawLead->created_at->format('Y-m-d H:i') : '-';
                $viewUrl = route('backend.rawleadform.records.show', $rawLead->id);
                $editUrl = route('rawlead.edit', $rawLead->id);
                $deleteUrl = route('rawlead.destroy', $rawLead->id);

                $tableHtml .= '<tr>';
                $tableHtml .= '<td><input type="checkbox" class="form-check-input rawlead-checkbox" name="raw_lead_ids[]" value="' . $rawLead->id . '"></td>';
                // Remove the image element from the name cell in the AJAX response
                $tableHtml .= '<td><div class="d-flex align-items-center"><div class="ms-3"><h6 class="user-name mb-0">' . e($rawLead->name ?? '-') . '</h6></div></div></td>';
                $tableHtml .= '<td>' . e($rawLead->email ?? '-') . '</td>';
                $tableHtml .= '<td>' . e($rawLead->phone ?? '-') . '</td>';
                $tableHtml .= '<td><span class="badge ' . e($statusClass) . ' status-default">' . e($statusText) . '</span></td>';
                $tableHtml .= '<td>' . e($assigneeName) . '</td>';
                $tableHtml .= '<td>' . e($rawLead->preferred_country ?? '-') . '</td>';
                $tableHtml .= '<td>' . e($applyingForText) . '</td>';
                $tableHtml .= '<td>' . e($createdAt) . '</td>';
                $tableHtml .= '<td><div class="dropdown dropstart"><a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton' . $rawLead->id . '" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical fs-6"></i></a><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $rawLead->id . '">';
                $tableHtml .= '<li><a class="dropdown-item d-flex align-items-center gap-3" href="' . $viewUrl . '"><i class="fs-4 ti ti-eye"></i> View</a></li>';
                $tableHtml .= '<li><a class="dropdown-item d-flex align-items-center gap-3" href="' . $editUrl . '"><i class="fs-4 ti ti-edit"></i> Edit</a></li>';
                $tableHtml .= '<li><form action="' . $deleteUrl . '" method="POST" onsubmit="return confirmDelete(this);" style="display: inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="dropdown-item d-flex align-items-center gap-3 text-danger" style="border: none; background: none; width: 100%; text-align: left; padding: 0.25rem 1rem;"><i class="fs-4 ti ti-trash"></i> Delete</button></form></li>';
                $tableHtml .= '</ul></div></td>';
                $tableHtml .= '</tr>';
            }
        }

        // Define inline styles
        $paginationStyles = [
            'pagination' => 'display: flex; flex-wrap: wrap; gap: 8px; margin: 10px 0; padding: 0; list-style: none; justify-content: center;',
            'page-item' => 'margin: 0;',
            'page-link' => 'padding: 0.6rem 1rem; font-size: 0.9rem; line-height: 1.5; border-radius: 6px; color: #495057; text-decoration: none; border: 1px solid #dee2e6; background-color: #fff; transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; display: block;',
            'page-item-active' => 'background-color: #007bff; color: white; border-color: #007bff; box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);',
            'page-link-hover' => 'background-color: #e9ecef; color: #0056b3; border-color: #adbac7;',
            'page-item-disabled' => 'color: #adb5bd; pointer-events: none; background-color: #fff; border-color: #dee2e6;',
            'page-button' => 'border-radius: 6px;',
            'page-button-first' => 'margin-right: 4px;',
            'page-button-last' => 'margin-left: 4px;'
        ];


        // Return only the table HTML
        return response()->json([
            'table_html' => $tableHtml,
            'links' => (string) $rawLeads->links(),
            'pagination_styles' => $paginationStyles  // Pass the styles to the view
        ]);
    }

 public function exportAllData()
{
    try {
        // Fetch all raw leads with relationships
        $rawLeads = RawLead::with(['creator', 'assignee'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers without ID
        $headers = [
            'Name',
            'Email',
            'Phone',
            'Status',
            'Ad ID',
            'Country Preference',
            'Subject Preference',
            'Applying For',
            'Assigned To',
            'Created By',
            'Date Created',
            'Last Updated',
            'Initial Comment'
        ];

        // Set header row using column letters
        foreach ($headers as $index => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Fill data rows (without ID)
        $rowNumber = 2;
        foreach ($rawLeads as $lead) {
            $col = 1;

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

        // Set headers for download
        $fileName = "raw_leads_export_" . now()->format('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

    } catch (\Exception $e) {
        \Log::error("Export error: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error exporting data.');
    }
}
}