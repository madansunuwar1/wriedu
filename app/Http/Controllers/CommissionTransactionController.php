<?php

namespace App\Http\Controllers;

use App\Models\CommissionTransaction;
use App\Models\Comission;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommissionTransactionController extends Controller
{
    public function index()
    {
        $commissionTransactions = CommissionTransaction::all();
        return view('commission_transactions.index', compact('commissionTransactions'));
    }

    public function create()
    {
        return view('commission_transactions.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'commission_id' => 'required|exists:comissions,id',
            'application_id' => 'nullable|exists:applications,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'paid' => 'nullable|string',
            'status' => 'required|string',
            'due_date' => 'nullable|date',
            'commissionpayable_id' => 'nullable|exists:comission_payables,id', // Added this field
        ]);

        // Auto-determine type based on business logic
        $validatedData['type'] = $this->determineTransactionType($validatedData, 'create');

        CommissionTransaction::create($validatedData);

        return redirect()->route('commission_transactions.index')->with('success', 'Commission transaction created successfully.');
    }

    public function show(CommissionTransaction $commissionTransaction)
    {
        return view('commission_transactions.show', compact('commissionTransaction'));
    }

    public function edit(CommissionTransaction $commissionTransaction)
    {
        return view('commission_transactions.edit', compact('commissionTransaction'));
    }

    public function update(Request $request, $applicationId)
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

            $transactionId = $request->input('commission_transaction_id');

            if (!$transactionId) {
                Log::warning('Transaction ID is missing');
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID is required for a full update.',
                ], 400);
            }

            $validatedData = $request->validate([
                'commission_id' => 'required|exists:comissions,id',
                'application_id' => 'nullable|exists:applications,id',
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'exchange_rate' => 'required|numeric|min:0',
                'paid' => 'nullable|string',
                'status' => 'required|string',
                'due_date' => 'nullable|date',
                'commissionpayable_id' => 'nullable|exists:comission_payables,id',
            ]);

            // Auto-determine type based on business logic
            $validatedData['type'] = $this->determineTransactionType($validatedData, 'full_update');

            $commissionTransaction = CommissionTransaction::findOrFail($transactionId);

            Log::info('Updating Commission Transaction (Full Update)', [
                'transaction_id' => $commissionTransaction->id,
                'old_data' => $commissionTransaction->toArray(),
                'new_data' => $validatedData,
                'auto_determined_type' => $validatedData['type'],
                'updated_by_user_id' => Auth::id(),
            ]);

            // Update the commission transaction with the validated data
            $commissionTransaction->update($validatedData);

            Log::info('Commission transaction updated successfully with type: ' . $validatedData['type']);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'type' => $validatedData['type'],
                'transaction_id' => $commissionTransaction->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Commission Transaction Full Update Validation Failed', [
                'request_data' => $request->all(),
                'validation_errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            Log::error('Commission Transaction Full Update Model Not Found', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Commission transaction not found for full update',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Commission Transaction Full Update Error', [
                'application_id_from_route' => $applicationId,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }


// In your controller










    /**
     * Automatically determine transaction type based on exchange_rate and paid status
     */
    private function determineTransactionType($data, $updatedField = null)
    {
        Log::info('Determining transaction type', [
            'data' => $data,
            'updated_field' => $updatedField
        ]);
        
        // If commissionpayable_id exists, it's a payable transaction
        if (isset($data['commissionpayable_id']) && !empty($data['commissionpayable_id'])) {
            Log::info('Type determined as payable due to commissionpayable_id');
            return 'payable';
        }
        
        // Check if both exchange_rate and paid are set/updated
        $hasExchangeRate = isset($data['exchange_rate']) && !empty($data['exchange_rate']) && $data['exchange_rate'] > 0;
        $hasPaidStatus = isset($data['paid']) && !empty($data['paid']) && $this->isPaidStatus($data['paid']);
        
        Log::info('Checking exchange_rate and paid status', [
            'has_exchange_rate' => $hasExchangeRate,
            'exchange_rate_value' => $data['exchange_rate'] ?? null,
            'has_paid_status' => $hasPaidStatus,
            'paid_value' => $data['paid'] ?? null,
            'updated_field' => $updatedField
        ]);
        
        // If exchange_rate is being updated and is greater than 0
        if ($updatedField === 'exchange_rate' && $hasExchangeRate) {
            Log::info('Type determined as payable due to exchange_rate update');
            return 'payable';
        }
        
        // If paid status is being updated to a "paid" status
        if ($updatedField === 'paid' && $hasPaidStatus) {
            Log::info('Type determined as payable due to paid status update');
            return 'payable';
        }
        
        // If both exchange_rate and paid are set (indicating payment processing)
        if ($hasExchangeRate && $hasPaidStatus) {
            Log::info('Type determined as payable due to both exchange_rate and paid status');
            return 'payable';
        }
        
        // If status indicates payment completion
        if (isset($data['status']) && in_array(strtolower($data['status']), ['completed', 'paid', 'processed'])) {
            Log::info('Type determined as payable due to completed status');
            return 'payable';
        }
        
        // Default to receivable for new/pending commissions
        Log::info('Type determined as receivable (default)');
        return 'receivable';
    }

    /**
     * Get display-friendly type name
     */
    public function getDisplayType($type)
    {
        return $type === 'payable' ? 'Commission Payable' : 'Commission Receivable';
    }

private function updateField(Request $request, $applicationId)
{
    DB::beginTransaction();

    try {
        $field = $request->input('field');
        $value = $request->input('value');
        $transactionType = $request->input('type', 'receivable');
        $commissionPayableId = $request->input('commissionpayable_id');

        Log::info('Field Update Request Started', [
            'field' => $field,
            'value' => $value,
            'application_id' => $applicationId,
            'transactionType' => $transactionType,
            'commissionpayable_id' => $commissionPayableId
        ]);

        $allowedFields = ['paid', 'exchange_rate', 'amount', 'status', 'due_date'];
        if (!in_array($field, $allowedFields)) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Field not allowed for update',
            ], 400);
        }

        $processedValue = $this->processFieldValue($field, $value);

        // Build query based on transaction type
        $query = CommissionTransaction::where('application_id', $applicationId)
            ->where('type', $transactionType);

        // For payable transactions, include commissionpayable_id in query if provided
        if ($transactionType === 'payable' && $commissionPayableId) {
            $query->where('commissionpayable_id', $commissionPayableId);
        }

        $transaction = $query->first();

        // If no transaction found, create new one with correct type
        if (!$transaction) {
            $transaction = new CommissionTransaction();
            $this->initializeNewTransaction($transaction, $applicationId, $transactionType, $commissionPayableId);
        }

        // Update the field
        $transaction->{$field} = $processedValue;

        // For payable transactions, ensure commissionpayable_id is set if provided
        if ($transactionType === 'payable' && $commissionPayableId) {
            $transaction->commissionpayable_id = $commissionPayableId;
        }

        $transaction->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully',
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'commissionpayable_id' => $transaction->commissionpayable_id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Field Update Error', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage(),
        ], 500);
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

  private function initializeNewTransaction(&$transaction, $applicationId, $transactionType = 'receivable', $commissionPayableId = null)
{
    $application = Application::findOrFail($applicationId);
    $commission = $this->getCommissionForApplication($applicationId);

    Log::info('Initializing new transaction', [
        'application_id' => $applicationId,
        'transaction_type' => $transactionType,
        'commission_id' => $commission ? $commission->id : null,
        'commissionpayable_id' => $commissionPayableId
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
        'type' => $transactionType,
        'commissionpayable_id' => $transactionType === 'payable' ? $commissionPayableId : null
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
                $percentage = (float) str_replace('%', '', $rateValue);
                $calculatedAmount = (float)$application->fee * ($percentage / 100);
            } else {
                // Handle fixed amount
                $calculatedAmount = (float) $rateValue;
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

    public function destroy(CommissionTransaction $commissionTransaction)
    {
        $commissionTransaction->delete();
        return redirect()->route('commission_transactions.index')->with('success', 'Commission transaction deleted successfully.');
    }
}