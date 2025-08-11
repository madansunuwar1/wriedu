<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comission;
use App\Models\Application;
use App\Models\CommissionHistory;
use App\Models\CommissionPayable;
use App\Models\CommissionTransaction;
use App\Models\Finance;
use App\Models\Partner;
use App\Models\DataEntry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReceivableApiController extends Controller
{
    // Existing methods from the second file
    public function getFormData()
    {
        try {
            $data_entries = DataEntry::select('id', 'newUniversity')
                ->whereNotNull('newUniversity')
                ->distinct()
                ->get();

            $partners = Partner::select('id', 'agency_name')
                ->whereNotNull('agency_name')
                ->get();

            return response()->json([
                'success' => true,
                'data_entries' => $data_entries,
                'partners' => $partners,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching form data for commission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve form data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $commissions = Comission::latest()->get();
            return response()->json($commissions);
        } catch (Exception $e) {
            Log::error('Error in ComissionController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve commissions.'], 500);
        }
    }
    public function commissionhistoryindex()
    {
        try {
            $commissions_history = CommissionHistory::latest()->get();
            return response()->json($commissions_history);
        } catch (Exception $e) {
            Log::error('Error in ComissionController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve commissions.'], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'university' => 'required|string|max:255',
            'product' => 'required|string|max:255',

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
            $commission = Comission::create([
                'university' => $request->university,
                'product' => $request->product,

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
            Log::error('Error creating Comission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create commission rate: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showDefault()
    {
        return response()->json([
            'success' => true,
            'data' => new Comission(),
        ]);
    }

    public function show(Comission $commission)
    {
        return response()->json([
            'success' => true,
            'data' => $commission
        ], 200);
    }

    public function update(Request $request, Comission $commission)
    {
        $validator = Validator::make($request->all(), [
            'university' => 'sometimes|string|max:255',
            'product' => 'sometimes|string|max:255',
            'partner' => 'sometimes|string|max:255',
            'has_progressive_commission' => 'sometimes|string|in:yes,no',
            'progressive_commission' => 'sometimes|nullable|string|max:255',
            'has_bonus_commission' => 'sometimes|string|in:yes,no',
            'bonus_commission' => 'sometimes|nullable|string|max:255',
            'has_incentive_commission' => 'sometimes|string|in:yes,no',
            'incentive_commission' => 'sometimes|nullable|string|max:255',
            'commissionTypes' => 'sometimes|array',
            'commissionValues' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $updatePayload = [];

        try {
            $directFields = ['university', 'product', 'partner'];
            foreach ($directFields as $field) {
                if (array_key_exists($field, $validatedData)) {
                    $updatePayload[$field] = $validatedData[$field];
                }
            }

            if (array_key_exists('has_progressive_commission', $validatedData)) {
                $isProgressive = $validatedData['has_progressive_commission'] === 'yes';
                $updatePayload['has_progressive_commission'] = $isProgressive;
                $updatePayload['progressive_commission'] = $isProgressive ? ($validatedData['progressive_commission'] ?? null) : null;
            }

            if (array_key_exists('has_bonus_commission', $validatedData)) {
                $isBonus = $validatedData['has_bonus_commission'] === 'yes';
                $updatePayload['has_bonus_commission'] = $isBonus;
                $updatePayload['bonus_commission'] = $isBonus ? ($validatedData['bonus_commission'] ?? null) : null;
            }

            if (array_key_exists('has_incentive_commission', $validatedData)) {
                $isIntensive = $validatedData['has_incentive_commission'] === 'yes';
                $updatePayload['has_incentive_commission'] = $isIntensive;
                $updatePayload['incentive_commission'] = $isIntensive ? ($validatedData['incentive_commission'] ?? null) : null;
            }

            if (array_key_exists('commissionTypes', $validatedData)) {
                if (is_array($validatedData['commissionTypes'])) {
                    if ($this->isAssociativeArray($validatedData['commissionTypes'])) {
                        $existingTypes = $commission->commission_types ?? [];
                        $newTypes = array_merge($existingTypes, $validatedData['commissionTypes']);
                        $updatePayload['commission_types'] = $newTypes;
                    } else {
                        if (array_key_exists('commissionValues', $validatedData)) {
                            $updatePayload['commission_types'] = $this->filterCommissionValues($validatedData['commissionTypes'], $validatedData['commissionValues']);
                        }
                    }
                }
            }

            if (empty($updatePayload)) {
                return response()->json(['success' => false, 'message' => 'No valid fields provided for update.'], 400);
            }

            $commission->update($updatePayload);

            return response()->json(['success' => true, 'message' => 'Commission updated successfully.', 'data' => $commission->fresh()]);

        } catch (Exception $e) {
            Log::error("Error updating commission #{$commission->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update commission.'], 500);
        }
    }

    public function addCommissionType(Request $request, Comission $commission)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $existingTypes = $commission->commission_types ?? [];
            $existingTypes[$request->type] = $request->value;

            $commission->update(['commission_types' => $existingTypes]);

            return response()->json(['success' => true, 'message' => 'Commission type added successfully.', 'data' => $commission->fresh()]);

        } catch (Exception $e) {
            Log::error("Error adding commission type to commission #{$commission->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add commission type.'], 500);
        }
    }

    public function deleteCommissionType(Comission $commission, $type)
    {
        try {
            $existingTypes = $commission->commission_types ?? [];

            if (!isset($existingTypes[$type])) {
                return response()->json(['success' => false, 'message' => 'Commission type not found.'], 404);
            }

            unset($existingTypes[$type]);

            $commission->update(['commission_types' => $existingTypes]);

            return response()->json(['success' => true, 'message' => 'Commission type deleted successfully.', 'data' => $commission->fresh()]);

        } catch (Exception $e) {
            Log::error("Error deleting commission type from commission #{$commission->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete commission type.'], 500);
        }
    }

    public function destroy(Comission $commission)
    {
        try {
            $commission->delete();
            return response()->json(['message' => 'Commission record deleted successfully.']);
        } catch (Exception $e) {
            Log::error("Error deleting commission #{$commission->id}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete commission record.'], 500);
        }
    }

    private function filterCommissionValues(array $types, array $values): array
    {
        $result = [];
        foreach ($types as $type) {
            if (isset($values[$type]) && !empty($values[$type])) {
                $result[$type] = $values[$type];
            }
        }
        return $result;
    }

    private function isAssociativeArray(array $array): bool
    {
        if (array() === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public function getFinanceCommissionData()
    {
        try {
            $commission_payable = CommissionPayable::all();
            $commissions = Comission::all();
            return response()->json(compact('commission_payable', 'commissions'));
        } catch (Exception $e) {
            Log::error('Failed to load finance commission data: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load finance commission data.'], 500);
        }
    }


    // Existing methods from the first file
    public function accountreceivableview($id)
    {
        Log::info("Accessing accountreceivableview with Application ID: {$id}");

        try {
            $application = Application::with(['commissionTransaction' => function ($query) {
                    $query->where('type', 'receivable');
                }])->find($id);

            if (!$application) {
                Log::warning("Application with ID {$id} not found.");
                return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
            }

            $applications = collect([$application]);
            $finances = Finance::all();
            $commissions = Comission::all();
            $commissionTransactions = CommissionTransaction::where('type', 'receivable')->get();

            //  Log the commission transactions
            Log::info('Application with commission transaction', [
                'commissionTransactions' =>  $application->commissionTransaction,
            ]);

            return response()->json([
                'success' => true,
                'applications' => $applications,
                'finances' => $finances,
                'commissions' => $commissions,
                'commissionTransactions' => $commissionTransactions,
                'finances_count' => $finances->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching account receivable view', [
                'application_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch account receivable data.'], 500);
        }
    }

    public function updatecommission(Request $request, $applicationId)
    {
        Log::info('Update method started', [
            'application_id' => $applicationId,
            'request_data' => $request->all()
        ]);

        try {
            $isPartialUpdate = $request->has('field') && $request->has('value');

            if ($isPartialUpdate) {
                Log::info('Handling partial update');
                return $this->updateField($request, $applicationId);
            }

            // Full Update logic (if not a partial update)
            $validator = Validator::make($request->all(), [
                'commission_id' => 'required|exists:comissions,id',
                'application_id' => 'nullable|exists:applications,id',
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'exchange_rate' => 'required|numeric|min:0',
                'paid' => 'nullable|string',
                'status' => 'required|string',
                'due_date' => 'nullable|date',
                'commission_transaction_id' => 'required|exists:commission_transactions,id', // Ensure transaction_id is provided
            ]);

            if ($validator->fails()) {
                Log::error('Validation Failed for Full Update', ['errors' => $validator->errors()]);
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            $validatedData = $validator->validated();

             // Auto-determine type based on business logic
            $validatedData['type'] = 'receivable'; // Hardcoded Receivable Type

            $commissionTransaction = CommissionTransaction::findOrFail($validatedData['commission_transaction_id']);

            Log::info('Updating Commission Transaction (Full Update)', [
                'transaction_id' => $commissionTransaction->id,
                'old_data' => $commissionTransaction->toArray(),
                'new_data' => $validatedData,
                'auto_determined_type' => $validatedData['type'],
                'updated_by_user_id' => Auth::id(),
            ]);

            $commissionTransaction->update($validatedData);

            Log::info('Commission transaction updated successfully with type: ' . $validatedData['type']);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'type' => $validatedData['type'],
                'transaction_id' => $commissionTransaction->id
            ]);

        }  catch (ModelNotFoundException $e) {
            Log::error('Model Not Found for Full Update', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Commission transaction not found.'], 404);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction in case of errors
            Log::error('Error updating commission transaction', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update commission transaction.'], 500);
        }
    }
private function updateField(Request $request, $applicationId)
{
    DB::beginTransaction();
    try {
        $validator = Validator::make($request->all(), [
            'field' => 'required|string|in:paid,exchange_rate,amount,status,due_date',
            'value' => 'nullable',
            'type' => 'required|string|in:receivable',
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            Log::error('Validation Failed for Field Update', ['errors' => $validator->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        Log::info('Field Update Request Started', [
            'field' => $validatedData['field'],
            'value' => $validatedData['value'],
            'application_id' => $applicationId,
            'transactionType' => $validatedData['type'],
        ]);

        // Find the transaction without modifying it yet
        $transaction = CommissionTransaction::where('application_id', $applicationId)
            ->where('type', $validatedData['type'])
            ->first();

        if (!$transaction) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Commission transaction not found for this application.',
            ], 404);
        }

        // Store the current commission_id before any updates
        $currentCommissionId = $transaction->commission_id;

        // Process Field Value
        $processedValue = $this->processFieldValue($validatedData['field'], $validatedData['value']);

        // Update only the specific field
        $transaction->{$validatedData['field']} = $processedValue;

        // Preserve the commission_id
        $transaction->commission_id = $currentCommissionId;

        $transaction->save();
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully',
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'commission_id' => $transaction->commission_id,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating field', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to update field.'], 500);
    }
}


    private function getCommissionForApplication($applicationId)
    {
        try {
            $application = Application::findOrFail($applicationId);

            $university = trim($application->university);
            $product = trim($application->english); // english column is the product

            Log::info('Looking for commission', [
                'university' => $university,
                'product' => $product,
                'application_id' => $applicationId
            ]);

            // Try exact match first
            $commission = Comission::where(DB::raw('TRIM(university)'), $university)
                ->where(DB::raw('TRIM(product)'), $product)
                ->first();

            // If no exact match, try university-only match
            if (!$commission) {
                $commission = Comission::where(DB::raw('TRIM(university)'), $university)
                    ->whereNull('product')
                    ->orWhere('product', '')
                    ->first();
            }

            // If still no match, try product-only match
            if (!$commission) {
                $commission = Comission::where(DB::raw('TRIM(product)'), $product)
                    ->whereNull('university')
                    ->orWhere('university', '')
                    ->first();
            }

            Log::info('Commission found', [
                'commission_id' => $commission ? $commission->id : null,
                'commission_data' => $commission ? $commission->toArray() : null
            ]);

            return $commission;
        } catch (\Exception $e) {
            Log::error('Error getting commission for application', [
                'application_id' => $applicationId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function processFieldValue($field, $value)
    {
        switch ($field) {
            case 'paid':
                return $value === null || $value === '' ? null : (string)$value;

            case 'amount':
            case 'exchange_rate':
                if ($value === null || $value === '') {
                    return $field === 'exchange_rate' ? 1.0 : 0.0;
                }
                $cleaned = is_string($value) ? preg_replace('/[^0-9.-]/', '', $value) : $value;
                return is_numeric($cleaned) ? (float)$cleaned : false;

            case 'due_date':
                if ($value === null || $value === '') {
                    return null;
                }
                try {
                    return Carbon::parse($value);
                } catch (\Exception $e) {
                    return false;
                }

            case 'status':
                return $value ? (string)$value : 'pending';

            default:
                return false;
        }
    }

    private function isPaidStatus($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        $stringValue = strtolower(trim((string)$value));
        $paidIndicators = ['paid', 'yes', 'true', '1', 'completed', 'done', 'success', 'processed'];

        Log::info('Checking paid status', [
            'original_value' => $value,
            'processed_value' => $stringValue,
            'is_paid' => in_array($stringValue, $paidIndicators)
        ]);

        return in_array($stringValue, $paidIndicators);
    }

    private function initializeNewTransaction(&$transaction, $applicationId, $transactionType = 'receivable')
    {
        $application = Application::findOrFail($applicationId);
        $commission = $this->getCommissionForApplication($applicationId);

        Log::info('Initializing new transaction', [
            'application_id' => $applicationId,
            'transaction_type' => $transactionType,
            'commission_id' => $commission ? $commission->id : null,
        ]);

        $transaction->fill([
            'commission_id' => $commission->id ?? null,
            'application_id' => $applicationId,
            'user_id' => Auth::id() ?? 1,
            'amount' => $this->calculateInitialAmount($application, $commission),
            'exchange_rate' => 1.0,
            'paid' => null,
            'status' => 'pending',
            'due_date' => null,
            'type' => $transactionType, // Ensure transaction type is set
        ]);
    }

    private function calculateInitialAmount($application, $commission)
    {
        if (!$application || !isset($application->fee) || $application->fee <= 0) {
            Log::warning('Invalid application fee', [
                'application_id' => $application->id ?? null,
                'fee' => $application->fee ?? null
            ]);
            return 0.0;
        }

        if (!$commission || !isset($commission->commission_types)) {
            Log::warning('No commission or commission types found', [
                'commission_id' => $commission->id ?? null,
                'commission_types' => $commission->commission_types ?? null
            ]);
            return 0.0;
        }

        $commissionTypes = is_string($commission->commission_types) ?
            json_decode($commission->commission_types, true) :
            $commission->commission_types;

        if (!is_array($commissionTypes)) {
            Log::warning('Invalid commission types format', [
                'commission_types' => $commission->commission_types
            ]);
            return 0.0;
        }

        Log::info('Calculating commission amount', [
            'application_fee' => $application->fee,
            'commission_types' => $commissionTypes
        ]);

        // Look for main commission rate (excluding bonus, incentive, etc.)
        foreach ($commissionTypes as $key => $rate) {
            $lowerKey = strtolower(trim($key));
            if (!in_array($lowerKey, ['bonus', 'intensive', 'incentive'])) {
                $rateValue = is_array($rate) && isset($rate['value']) ? $rate['value'] : $rate;

                // Handle percentage
                if (is_string($rateValue) && strpos($rateValue, '%') !== false) {
                    $percentage = (float)str_replace('%', '', $rateValue);
                    $calculatedAmount = (float)$application->fee * ($percentage / 100);
                } else {
                    // Handle fixed amount
                    $calculatedAmount = (float)$rateValue;
                }

                Log::info('Commission calculated', [
                    'rate_key' => $key,
                    'rate_value' => $rateValue,
                    'calculated_amount' => $calculatedAmount
                ]);

                return $calculatedAmount;
            }
        }

        Log::warning('No valid commission rate found, returning 0');
        return 0.0;
    }

   public function markAsReceived($applicationId)
{
    Log::info('Attempting to mark commission as received', ['application_id' => $applicationId]);

    try {
        // First, try to find in CommissionTransaction table using application_id
        $commissionTransaction = CommissionTransaction::where('application_id', $applicationId)->first();

        if ($commissionTransaction) {
            $commissionTransaction->update(['is_received' => true]);

            // Create a record in commission_history
            $this->createCommissionHistory($commissionTransaction);

            Log::info('Commission transaction marked as received successfully', ['application_id' => $applicationId]);
            return response()->json(['success' => true, 'message' => 'Commission transaction marked as received successfully']);
        }

        // If not found in CommissionTransaction, try Comission table using application_id
        $commission = Comission::where('application_id', $applicationId)->first();

        if ($commission) {
            $commission->update(['is_received' => true]);

            // Create a record in commission_history
            $this->createCommissionHistoryFromCommission($commission);

            Log::info('Commission marked as received successfully', ['application_id' => $applicationId]);
            return response()->json(['success' => true, 'message' => 'Commission marked as received successfully']);
        }

        // If not found in either table
        Log::warning('Commission/Transaction not found', ['application_id' => $applicationId]);
        return response()->json(['success' => false, 'message' => 'Commission not found'], 404);

    } catch (\Exception $e) {
        Log::error('Error marking commission as received', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to mark as received: ' . $e->getMessage()], 500);
    }
}

private function createCommissionHistory($commissionTransaction)
{
    $application = Application::find($commissionTransaction->application_id);
    
    // Get the related commission data using the commission_id from commission_transactions
    $commission = null;
    if ($commissionTransaction->commission_id) {
        $commission = Comission::find($commissionTransaction->commission_id);
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
        'status' => 'received',
        'received_at' => now(),
        'notes' => 'Commission marked as received'
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
        'type' => 'receivable',
        'commission_amount' => $commissionAmount,
        'bonus_amount' => $bonusAmount,
        'incentive_amount' => $incentiveAmount,
        'total_usd' => $totalUsd,
        'exchange_rate' => $exchangeRate,
        'total_npr' => $totalUsd * $exchangeRate,
        'paid_amount' => $commission->paid_amount ?? 0,
        'status' => 'received',
        'received_at' => now(),
        'notes' => 'Commission marked as received'
    ]);
}
}