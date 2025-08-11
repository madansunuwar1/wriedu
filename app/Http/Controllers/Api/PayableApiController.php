<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionPayable;
use App\Models\CommissionHistory;
use App\Models\Partner;
use App\Models\Application;
use App\Models\Finance;
use App\Models\Comission;
use App\Models\CommissionTransaction;
use App\Models\Store;
use App\Models\DataEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PayableApiController extends Controller
{
    public function index()
    {
        $commission_payable = CommissionPayable::latest()->get();
        return response()->json($commission_payable);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'university' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'partner' => 'required|int|max:255',
            'intake' => 'required|string|max:255',
            'has_progressive_commission' => 'required|string|in:yes,no',
            'progressive_commission' => 'nullable|string|max:255',
            'has_bonus_commission' => 'required|string|in:yes,no',
            'bonus_commission' => 'nullable|string|max:255',
            'has_incentive_commission' => 'required|string|in:yes,no',
            'incentive_commission' => 'nullable|string|max:255',
            'commissionTypes' => 'required|array|min:1',
            'commissionValues' => 'required|array|min:1',
        ]);

        $hasProgressiveCommission = $request->has_progressive_commission === 'yes';
        $hasBonusCommission = $request->has_bonus_commission === 'yes';
        $hasIncentiveCommission = $request->has_incentive_commission === 'yes';

        $commissionData = [];
        foreach ($request->commissionTypes as $type) {
            if (isset($request->commissionValues[$type])) {
                $commissionData[$type] = $request->commissionValues[$type];
            }
        }

        try {
            $commission = CommissionPayable::create([
                'university' => $request->university,
                'product' => $request->product,
                'partner' => $request->partner,
                'intake' => $request->intake,
                'has_progressive_commission' => $hasProgressiveCommission,
                'progressive_commission' => $hasProgressiveCommission ? $request->progressive_commission : null,
                'has_bonus_commission' => $hasBonusCommission,
                'bonus_commission' => $hasBonusCommission ? $request->bonus_commission : null,
                'has_incentive_commission' => $hasIncentiveCommission,
                'incentive_commission' => $hasIncentiveCommission ? $request->incentive_commission : null,
                'commission_types' => $commissionData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commission rate added successfully!',
                'data' => $commission
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating CommissionPayable: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create commission rate: ' . $e->getMessage()
            ], 500);
        }
    }


   public function accountpayableview($id)
{
    Log::info("Accessing accountpayableview with Application ID: {$id}");
    $application = Application::with(['payableCommissionTransaction' => function ($query) {
        $query->where('type', 'payable'); // Ensure this matches what you need
    }])->find($id);

    if (!$application) {
        Log::warning("Application with ID {$id} not found.");
        return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
    }

    $applications = collect([$application]);
    $finances = Finance::all();
    $commissions = CommissionPayable::all();
    $stores = Store::all();
    $commissionTransactions = CommissionTransaction::all();

    return response()->json([
        'success' => true,
        'applications' => $applications,
        'finances' => $finances,
        'commissions' => $commissions,
        'stores' => $stores,
        'commissionTransactions' => $commissionTransactions,
        'finances_count' => $finances->count(),
        'stores_count' => $stores->count(),
    ]);
}


    public function getintakes(): JsonResponse
    {
        try {
            $data_entries = DataEntry::select('id', 'newIntake', 'newUniversity')->get();

            return response()->json($data_entries);
        } catch (\Exception $e) {
            Log::error('Failed to fetch partners: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch partners',
                'message' => 'Unable to load partner data'
            ], 500);
        }
    }

    public function show($id)
    {
        $commissionPayable = CommissionPayable::findOrFail($id);
        return response()->json($commissionPayable);
    }

    public function update(Request $request, $id)
    {
        try {
            $commission = CommissionPayable::findOrFail($id);
            
            if ($request->has('commission_type') && $request->has('commission_value')) {
                $commissionType = $request->commission_type;
                $commissionValue = $request->commission_value;
                
                $commissionTypes = is_array($commission->commission_types) 
                    ? $commission->commission_types 
                    : [];
                
                $commissionTypes[$commissionType] = $commissionValue;
                
                $commission->commission_types = $commissionTypes;
                $commission->save();
                
                return response()->json([
                    'success' => true,
                    'message' => "$commissionType commission updated successfully"
                ]);
            }
            
            if ($request->has('has_bonus_commission')) {
                $commission->has_bonus_commission = (bool)$request->has_bonus_commission;
                if (!$commission->has_bonus_commission && $request->filled('bonus_commission')) {
                    $commission->bonus_commission = null;
                } elseif ($request->filled('bonus_commission')) {
                    $commission->bonus_commission = $request->bonus_commission;
                }
            }
            
            if ($request->has('has_incentive_commission')) {
                $commission->has_incentive_commission = (bool)$request->has_incentive_commission;
                if (!$commission->has_incentive_commission && $request->filled('incentive_commission')) {
                    $commission->incentive_commission = null;
                } elseif ($request->filled('incentive_commission')) {
                    $commission->incentive_commission = $request->incentive_commission;
                }
            }

            if ($request->has('has_progressive_commission')) {
                $commission->has_progressive_commission = (bool)$request->has_progressive_commission;
                if (!$commission->has_progressive_commission && $request->filled('progressive_commission')) {
                    $commission->progressive_commission = null;
                } elseif ($request->filled('progressive_commission')) {
                    $commission->progressive_commission = $request->progressive_commission;
                }
            }
            
            $fillableFields = [
                'university', 'product', 'partner', 'progressive_commission',
                'bonus_commission', 'incentive_commission'
            ];
            
            foreach ($fillableFields as $field) {
                if ($request->has($field)) {
                    $commission->$field = $request->$field;
                }
            }
            
            $commission->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Commission details updated successfully',
                'data' => $commission
            ]);
            
        } catch (Exception $e) {
            Log::error('Commission update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update commission details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $commission = CommissionPayable::findOrFail($id);
            $commission->delete();

            return response()->json(null, 204);

        } catch (Exception $e) {
            Log::error('Commission payable delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete commission record.'
            ], 500);
        }
    }

    public function exportPayable()
    {
        try {
            $commissions = CommissionPayable::all();
            
            $filename = 'commission_payable_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($commissions) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, [
                    'ID', 'University', 'Product', 'Partner', 'Commission Types',
                    'Has Progressive', 'Progressive Commission', 'Has Bonus', 'Bonus Commission',
                    'Has Incentive', 'Incentive Commission', 'Created At', 'Updated At'
                ]);

                foreach ($commissions as $commission) {
                    fputcsv($file, [
                        $commission->id,
                        $commission->university,
                        $commission->product,
                        $commission->partner,
                        is_array($commission->commission_types) ? json_encode($commission->commission_types) : $commission->commission_types,
                        $commission->has_progressive_commission ? 'Yes' : 'No',
                        $commission->progressive_commission,
                        $commission->has_bonus_commission ? 'Yes' : 'No',
                        $commission->bonus_commission,
                        $commission->has_incentive_commission ? 'Yes' : 'No',
                        $commission->incentive_commission,
                        $commission->created_at,
                        $commission->updated_at,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('Commission payable export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export commission data.');
        }
    }

    public function importPayable(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048'
            ]);

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            $header = array_shift($data);
            
            $imported = 0;
            foreach ($data as $row) {
                CommissionPayable::create([
                    'university' => $row[0] ?? '',
                    'product' => $row[1] ?? '',
                    'partner' => $row[2] ?? '',
                    'commission_types' => isset($row[3]) ? json_decode($row[3], true) : null,
                    'has_progressive_commission' => filter_var($row[4] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'progressive_commission' => $row[5] ?? null,
                    'has_bonus_commission' => filter_var($row[6] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'bonus_commission' => $row[7] ?? null,
                    'has_incentive_commission' => filter_var($row[8] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'incentive_commission' => $row[9] ?? null,
                ]);
                $imported++;
            }

            return response()->json(['success' => true, 'message' => "Successfully imported {$imported} commission records."]);
        } catch (Exception $e) {
            Log::error('Commission payable import error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to import commission data.'], 500);
        }
    }
    
    public function getPartners(): JsonResponse
    {
        try {
            $partners = Partner::select('id', 'agency_name')->get();

            return response()->json($partners);
        } catch (\Exception $e) {
            Log::error('Failed to fetch partners: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch partners',
                'message' => 'Unable to load partner data'
            ], 500);
        }
    }

    public function updatecommission($appId, Request $request) {
        Log::info("Starting updatecommission for app ID: $appId");

        $validated = $request->validate([
            'field' => 'required|in:exchange_rate,paid',
            'value' => 'required|numeric',
            'type' => 'required|in:payable,receivable'
        ]);
        Log::info("Request validated", $validated);

        try {
            DB::beginTransaction();
            Log::info("Database transaction started for app ID: $appId");

            $application = Application::findOrFail($appId);
            Log::info("Application found", ['appId' => $appId]);

            // Debug: Log application data to understand structure
            Log::info("Application data", [
                'id' => $application->id,
                'university' => $application->university ?? 'NULL',
                'english' => $application->english ?? 'NULL', 
                'partner' => $application->partner ?? 'NULL',
                'partnerDetails' => $application->partnerDetails ?? 'NULL',
                'fee' => $application->fee ?? 'NULL'
            ]);

            // Find existing transaction with matching type
            $transaction = $application->commissionTransaction()
                ->where('type', $validated['type'])
                ->first();

            if (!$transaction) {
                Log::info("No existing transaction found for type: {$validated['type']}");

                // Find the correct commission payable record with improved matching
                $commissionPayable = $this->findCommissionPayable($application);

                if (!$commissionPayable) {
                    Log::error("No commission payable found for application ID: $appId");
                    
                    // Enhanced debugging: Let's see what commission payables exist
                    $allCommissions = CommissionPayable::all();
                    Log::info("Available commission payables:", $allCommissions->toArray());
                    
                    throw new \Exception('No commission payable found for this application. Please check if commission rates are configured for this university/product combination.');
                }

                Log::info("Commission payable found", ['commissionPayableId' => $commissionPayable->id]);

                // Create new transaction since none exists with the requested type
                $transaction = new CommissionTransaction();
                $transaction->application_id = $appId;
                $transaction->commissionpayable_id = $commissionPayable->id;
                $transaction->user_id = auth()->id();
                $transaction->type = $validated['type'];
                $transaction->status = 'pending';

                if (empty($transaction->amount)) {
                    $transaction->amount = $this->calculateCommissionAmount($application);
                    Log::info("Commission amount calculated", ['amount' => $transaction->amount]);
                }
            }

            // Update the specified field
            $transaction->{$validated['field']} = $validated['value'];
            $transaction->save();
            Log::info("Transaction updated", ['field' => $validated['field'], 'value' => $validated['value']]);

            DB::commit();
            Log::info("Database transaction committed for app ID: $appId");

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $validated['value'],
                'transaction' => [
                    'id' => $transaction->id,
                    'commissionpayable_id' => $transaction->commissionpayable_id,
                    'type' => $transaction->type
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error for app ID: $appId", ['errors' => $e->errors()]);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Update failed for app ID: $appId", ['error' => $e->getMessage()]);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findCommissionPayable($application) {
        if (!$application) {
            Log::warning("Application is null in findCommissionPayable");
            return null;
        }

        // Enhanced debugging
        Log::info("Finding commission payable for application", [
            'app_id' => $application->id,
            'university_raw' => $application->university,
            'english_raw' => $application->english,
            'partner_raw' => $application->partner,
            'partnerDetails_raw' => $application->partnerDetails
        ]);

        // Get university with improved handling
        $university = null;
        if ($application->relationLoaded('university_relation') && $application->university_relation) {
            $university = $application->university_relation->name ?? $application->university_relation->id ?? null;
        } else {
            $university = $application->university ?? null;
        }
        
        // Get product from 'english' column 
        $product = $application->english ?? null;
        
        // Get partner with multiple fallbacks
        $partner = null;
        if ($application->relationLoaded('partnerDetails') && $application->partnerDetails) {
            $partner = $application->partnerDetails->id ?? $application->partnerDetails;
        } else {
            $partner = $application->partner ?? null;
        }

        Log::info("Processed application data for matching", [
            'university' => $university,
            'product' => $product,
            'partner' => $partner
        ]);
        
        if (!$university || !$product) {
            Log::warning("Missing university ({$university}) or product ({$product}) for application {$application->id}");
            return null;
        }

        // Strategy 1: Exact match (university, product, partner)
        if ($partner) {
            $commissionPayable = CommissionPayable::where('university', $university)
                ->where('product', $product)
                ->where('partner', $partner)
                ->first();
                
            if ($commissionPayable) {
                Log::info("Found exact match commission payable", ['id' => $commissionPayable->id]);
                return $commissionPayable;
            }
        }

        // Strategy 2: University and product match (ignore partner)
        $commissionPayable = CommissionPayable::where('university', $university)
            ->where('product', $product)
            ->first();
            
        if ($commissionPayable) {
            Log::info("Found university+product match commission payable", ['id' => $commissionPayable->id]);
            return $commissionPayable;
        }

        // Strategy 3: Case-insensitive matching for university and product
        $commissionPayable = CommissionPayable::whereRaw('LOWER(university) = LOWER(?)', [$university])
            ->whereRaw('LOWER(product) = LOWER(?)', [$product])
            ->first();
            
        if ($commissionPayable) {
            Log::info("Found case-insensitive match commission payable", ['id' => $commissionPayable->id]);
            return $commissionPayable;
        }

        // Strategy 4: Partial matching using LIKE
        $commissionPayable = CommissionPayable::where('university', 'LIKE', "%{$university}%")
            ->where('product', 'LIKE', "%{$product}%")
            ->first();
            
        if ($commissionPayable) {
            Log::info("Found partial match commission payable", ['id' => $commissionPayable->id]);
            return $commissionPayable;
        }

        // Strategy 5: Just university match as fallback
        $commissionPayable = CommissionPayable::where('university', $university)
            ->whereNull('product')
            ->first();
            
        if ($commissionPayable) {
            Log::info("Found university-only match commission payable", ['id' => $commissionPayable->id]);
            return $commissionPayable;
        }

        Log::warning("No commission payable found for application", [
            'app_id' => $application->id,
            'university' => $university,
            'product' => $product,
            'partner' => $partner
        ]);

        return null;
    }

    protected function calculateCommissionAmount($application)
    {
        $fee = $application->fee ?? 0;
        return $fee * 0.1; // 10% commission example
    }





    public function markAsPaid($applicationId)
{
    Log::info('Attempting to mark commission as paid', ['application_id' => $applicationId]);

    try {
        // First, try to find in CommissionTransaction table using application_id
        $commissionTransaction = CommissionTransaction::where('application_id', $applicationId)->first();

        if ($commissionTransaction) {
            $commissionTransaction->update(['is_paid' => true]);

            // Create a record in commission_history
            $this->createCommissionHistory($commissionTransaction);

            Log::info('Commission transaction marked as paid successfully', ['application_id' => $applicationId]);
            return response()->json(['success' => true, 'message' => 'Commission transaction marked as paid successfully']);
        }

        // If not found in CommissionTransaction, try Comission table using application_id
        $commission = Comission::where('application_id', $applicationId)->first();

        if ($commission) {
            $commission->update(['is_paid' => true]);

            // Create a record in commission_history
            $this->createCommissionHistoryFromCommission($commission);

            Log::info('Commission marked as paid successfully', ['application_id' => $applicationId]);
            return response()->json(['success' => true, 'message' => 'Commission marked as paid successfully']);
        }

        // If not found in either table
        Log::warning('Commission/Transaction not found', ['application_id' => $applicationId]);
        return response()->json(['success' => false, 'message' => 'Commission not found'], 404);

    } catch (\Exception $e) {
        Log::error('Error marking commission as paid', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to mark as paid: ' . $e->getMessage()], 500);
    }
}

private function createCommissionHistory($commissionTransaction)
{
    $application = Application::find($commissionTransaction->application_id);
    
    // Get the related commission data using the commissionpayable_id from commission_transactions
    $commission = null;
    if ($commissionTransaction->commissionpayable_id) {
        $commission = CommissionPayable::find($commissionTransaction->commissionpayable_id);
    }
    
    // Calculate total commission amount including bonus and incentive
    $commissionAmount = 0;
    $bonusAmount = 0;
    $incentiveAmount = 0;
    $progressiveAmount = 0;
    
    if ($commission) {
        // Get commission percentage from commission_types JSON and calculate amount
        $commissionTypes = $commission->commission_types;
        if ($commissionTypes) {
            $commissionPercentage = 0;
            
            if (is_array($commissionTypes)) {
                $commissionPercentage = (float) reset($commissionTypes);
            } elseif (is_string($commissionTypes)) {
                $decodedTypes = json_decode($commissionTypes, true);
                if ($decodedTypes && is_array($decodedTypes)) {
                    $commissionPercentage = (float) reset($decodedTypes);
                }
            }
            
            // Calculate commission amount from percentage and total fees
            if ($commissionPercentage > 0 && $application && $application->fee) {
                $commissionAmount = ($application->fee * $commissionPercentage) / 100;
            }
        }
        
        // Get bonus commission amount if it exists
        if ($commission->has_bonus_commission && $commission->bonus_commission) {
            $bonusAmount = (float) $commission->bonus_commission;
        }
        
        // Get incentive commission amount if it exists
        if ($commission->has_incentive_commission && $commission->incentive_commission) {
            $incentiveAmount = (float) $commission->incentive_commission;
        }
        
        // Get progressive commission amount if it exists
        if ($commission->has_progressive_commission && $commission->progressive_commission) {
            $progressiveAmount = (float) $commission->progressive_commission;
        }
    }
    
    $totalUsd = $commissionAmount + $bonusAmount + $incentiveAmount + $progressiveAmount;
    
    CommissionHistory::create([
        'application_id' => $commissionTransaction->application_id,
        'student_name' => $application ? $application->name : 'N/A',
        'university' => $application ? $application->university : ($commission ? $commission->university : 'N/A'),
        'intake' => $application ? $application->intake : ($commission ? $commission->intake : 'N/A'),
        'english' => $application ? $application->english : 'N/A',
        'type' => $commissionTransaction->type,
        'commission_amount' => $commissionAmount, // Calculated from percentage
        'bonus_amount' => $bonusAmount,
        'incentive_amount' => $incentiveAmount,
        'total_usd' => $totalUsd,
        'exchange_rate' => $commissionTransaction->exchange_rate ?? 1,
        'total_npr' => $totalUsd * ($commissionTransaction->exchange_rate ?? 1),
        'paid_amount' => $commissionTransaction->paid ?? 0,
        'status' => 'Paid',
        'paid_at' => now(),
        'notes' => 'Commission marked as paid'
    ]);
}

private function createCommissionHistoryFromCommission($commission)
{
    // Try to find the related application
    $application = null;
    if ($commission->application_id) {
        $application = Application::find($commission->application_id);
    }
    
    // Get commission amount - calculate from percentage and total fees
    $commissionAmount = 0;
    $commissionTypes = $commission->commission_types;
    
    if ($commissionTypes) {
        $commissionPercentage = 0;
        
        if (is_array($commissionTypes)) {
            $commissionPercentage = (float) reset($commissionTypes);
        } elseif (is_string($commissionTypes)) {
            $decodedTypes = json_decode($commissionTypes, true);
            if ($decodedTypes && is_array($decodedTypes)) {
                $commissionPercentage = (float) reset($decodedTypes);
            }
        }
        
        // Calculate commission amount from percentage and total fees
        if ($commissionPercentage > 0 && $application && $application->fee) {
            $commissionAmount = ($application->fee * $commissionPercentage) / 100;
        }
    }
    
    // If commission_types doesn't exist or calculation failed, check for a direct commission_amount field
    if ($commissionAmount == 0 && isset($commission->commission_amount)) {
        $commissionAmount = $commission->commission_amount;
    }
    
    // Get bonus, incentive, and progressive commission amounts
    $bonusAmount = 0;
    $incentiveAmount = 0;
    $progressiveAmount = 0;
    
    if ($commission->has_bonus_commission && $commission->bonus_commission) {
        $bonusAmount = (float) $commission->bonus_commission;
    }
    
    if ($commission->has_incentive_commission && $commission->incentive_commission) {
        $incentiveAmount = (float) $commission->incentive_commission;
    }
    
    if ($commission->has_progressive_commission && $commission->progressive_commission) {
        $progressiveAmount = (float) $commission->progressive_commission;
    }
    
    // FIXED: Changed from multiplication to addition
    $totalUsd = $commissionAmount + $bonusAmount + $incentiveAmount + $progressiveAmount;
    $exchangeRate = $commission->exchange_rate ?? 1;
    
    CommissionHistory::create([
        'application_id' => $commission->application_id,
        'student_name' => $application->name ?? 'N/A',
        'university' => $commission->university ?? 'N/A',
        'intake' => $commission->intake ?? 'N/A',
        'english' => $application->english ?? 'N/A',
        'type' => 'payable',
        'commission_amount' => $commissionAmount,
        'bonus_amount' => $bonusAmount,
        'incentive_amount' => $incentiveAmount,
        'total_usd' => $totalUsd,
        'exchange_rate' => $exchangeRate,
        'total_npr' => $totalUsd * $exchangeRate,
        'paid_amount' => $commission->paid_amount ?? 0,
        'status' => 'Paid',
        'paid_at' => now(),
        'notes' => 'Commission marked as paid'
    ]);
}

}