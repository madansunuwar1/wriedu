<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Form;
use App\Models\Store;
use App\Models\DataEntry;
use App\Models\Application;
use App\Models\Comission;
use App\Models\CommissionPayable;
use App\Models\CommissionTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
        $this->middleware('can:view_finances')->only('index', 'indexs');
        $this->middleware('can:create_finances')->only('create', 'store');
        $this->middleware('can:edit_finances')->only('edit', 'update');
        $this->middleware('can:delete_finances')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'receivable');
        $applications = collect();
        $comissions = collect();
        $commission_payable = collect();

        // Fetch only 'visa granted' applications
        $baseQuery = Application::where('status', 'visa granted');

        if ($view === 'receivable') {
            $applications = $baseQuery->get();
            $comissions = Comission::all();
            $commission_payable = CommissionPayable::all();
            $commission_transactions = CommissionTransaction::with('commission')->get();

            $applications = $this->processApplicationsForView($applications, $comissions, $commission_transactions, 'receivable');
        } elseif ($view === 'payable') {
            $applications = $baseQuery->get();
            $comissions = Comission::all();
            $commission_payable = CommissionPayable::all();
            $commission_transactions = CommissionTransaction::with('commissionPayable')->get();

            $applications = $this->processApplicationsForView($applications, $commission_payable, $commission_transactions, 'payable');
        }

        return view('backend.Finance.index', compact('view', 'applications', 'comissions', 'commission_payable'));
    }

    /**
     * Process applications for display in the view
     */
    protected function processApplicationsForView($applications, $commissionRules, $commissionTransactions, $viewType = 'receivable')
    {
        return $applications->map(function ($application) use ($commissionRules, $commissionTransactions, $viewType) {
            $matchingCommissionRule = $commissionRules->first(function ($rule) use ($application, $viewType) {
                // Add null safety checks
                if (!$rule || !$application) {
                    return false;
                }

                $universityMatch = strtolower(trim($rule->university ?? '')) === strtolower(trim($application->university ?? ''));
                $productMatch = strtolower(trim($rule->product ?? '')) === strtolower(trim($application->english ?? '')); // english is product
                
                // For payable view, match partner first, then university and product
                if ($viewType === 'payable') {
                    $partnerMatch = strtolower(trim($rule->partner ?? '')) === strtolower(trim($application->partnerDetails ?? ''));
                    if (!$partnerMatch) {
                        return false;
                    }
                    return $universityMatch && $productMatch;
                }
                
                // For receivable view, only match university and product
                return $universityMatch && $productMatch;
            });

            $matchingTransaction = $commissionTransactions->first(function ($transaction) use ($application) {
                return $transaction && $application && $transaction->application_id === $application->id;
            });

            $application->commission_rule = $matchingCommissionRule;
            $application->commission_transaction = $matchingTransaction;

            // Initialize default values
            $commissionTypesFromRule = [];
            $bonusFromRule = 0.0;
            $incentiveFromRule = 0.0;

            // Extract data from commission rule with null safety
            if ($matchingCommissionRule) {
                $commissionTypesFromRule = $this->parseCommissionTypes($matchingCommissionRule->commission_types ?? null);
                $bonusFromRule = (float)($matchingCommissionRule->bonus_commission ?? 0.0);
                $incentiveFromRule = (float)($matchingCommissionRule->incentive_commission ?? 0.0);
            }

            // Handle commission types override from transaction
            $displayedCommissionTypes = null;
            if ($matchingTransaction && !empty($matchingTransaction->commission_types_override)) {
                $displayedCommissionTypes = $this->parseCommissionTypes($matchingTransaction->commission_types_override);
            } else {
                $displayedCommissionTypes = $commissionTypesFromRule;
            }

            // Final values to display
            $displayedBonus = $bonusFromRule;
            $displayedIncentive = $incentiveFromRule;
            $displayedExchangeRate = $matchingTransaction ? (float) ($matchingTransaction->exchange_rate ?? 1.0) : 1.0;
            $displayedPaid = $matchingTransaction ? (float) ($matchingTransaction->paid ?? 0.0) : 0.0;
            $displayedCommissionValueString = $this->formatCommissionTypesForDisplay($displayedCommissionTypes);

            // Calculate commission from fee
            $calculatedCommissionAmountFromFee = $this->calculateCommissionFromFee(
                $displayedCommissionValueString,
                $application->fee ?? 0.0
            );

            // Calculate totals
            $totalValueUSD = $calculatedCommissionAmountFromFee + $displayedBonus + $displayedIncentive;
            $balanceDueUSD = max(0, $totalValueUSD - $displayedPaid);
            $balanceDueNPR = $balanceDueUSD * $displayedExchangeRate;

            // Attach calculated values to the application object
            $application->displayedCommissionValueString = $displayedCommissionValueString;
            $application->displayedBonus = $displayedBonus;
            $application->displayedIncentive = $displayedIncentive;
            $application->displayedExchangeRate = $displayedExchangeRate;
            $application->displayedPaid = $displayedPaid;
            $application->totalValueUSD = $totalValueUSD;
            $application->totalCommissionPayableNPR = $balanceDueNPR;
            $application->balanceDueUSD = $balanceDueUSD;
            $application->balanceDueNPR = $balanceDueNPR;
            $application->calculatedCommissionAmountFromFee = $calculatedCommissionAmountFromFee;

            return $application;
        });
    }

    /**
     * Parse commission types from various formats
     */
    protected function parseCommissionTypes($commissionTypes)
    {
        if (empty($commissionTypes)) {
            return [];
        }

        if (is_string($commissionTypes)) {
            $decoded = json_decode($commissionTypes, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : ['commission' => $commissionTypes];
        }

        return is_array($commissionTypes) ? $commissionTypes : ['commission' => (string) $commissionTypes];
    }

    /**
     * Format commission types for display
     */
    protected function formatCommissionTypesForDisplay($commissionTypes)
    {
        if (empty($commissionTypes)) {
            return 'N/A';
        }

        if (is_string($commissionTypes)) {
            return trim($commissionTypes);
        }

        $parts = [];
        foreach ($commissionTypes as $key => $value) {
            $cleanedKey = strtolower(trim($key));
            if (!in_array($cleanedKey, ['bonus', 'intensive', 'incentive'])) {
                $val = is_array($value) && isset($value['value']) ? $value['value'] : $value;
                if ($val !== null && $val !== '') {
                    $formattedVal = is_numeric($val) && strpos((string)$val, '%') === false ? $val . '%' : $val;
                    $parts[] = ucfirst($key) . ': ' . $formattedVal;
                }
            }
        }

        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Calculate commission from fee
     */
    protected function calculateCommissionFromFee($commissionValueString, $fee)
    {
        if ($commissionValueString === 'N/A' || $fee <= 0) {
            return 0.0;
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*%/i', $commissionValueString, $matches)) {
            $percentage = (float) $matches[1];
            return $fee * ($percentage / 100);
        }

        if (preg_match('/[\$]?(\d+(?:\.\d+)?)/i', $commissionValueString, $matches)) {
            $fixedAmount = (float) $matches[1];
            return $fixedAmount > 0 ? $fixedAmount : 0.0;
        }

        return 0.0;
    }

    /**
     * Find the appropriate commission payable record for the application
     * Updated to include partner matching with null safety
     */
    private function findCommissionPayable($application) {
        if (!$application) {
            Log::warning("Application is null in findCommissionPayable");
            return null;
        }

        // Get university - handle both string and ID cases with null safety
        $university = null;
        if ($application->relationLoaded('university_relation') && $application->university_relation) {
            // If university is a relationship - ADD NULL SAFETY HERE
            $university = $application->university_relation->name ?? $application->university_relation->id ?? null;
        } else {
            // If university is stored directly as string/ID
            $university = $application->university ?? null;
        }
        
        // Get product from 'english' column (as you mentioned it's the product)
        $product = $application->english ?? null; // This is the product according to your clarification
        
        // Get partner - ADD NULL SAFETY HERE
        $partner = $application->partnerDetails ?? $application->partner ?? null;
        
        if (!$university || !$product) {
            Log::warning("Missing university ({$university}) or product ({$product}) for application {$application->id}");
            return null;
        }

        // Find commission payable by university, product, and partner (exact match)
        $commissionPayable = CommissionPayable::where('university', $university)
            ->where('product', $product)
            ->where('partner', $partner)
            ->first();

        if (!$commissionPayable && $partner) {
            // Try to find without partner match if exact match fails
            $commissionPayable = CommissionPayable::where('university', $university)
                ->where('product', $product)
                ->whereNull('partner')
                ->first();
        }

        if (!$commissionPayable) {
            // Try to find a general commission payable for the university and partner
            $commissionPayable = CommissionPayable::where('university', $university)
                ->where('partner', $partner)
                ->whereNull('product')
                ->first();
        }

        if (!$commissionPayable) {
            // Finally, try to find a general commission payable for just the university
            $commissionPayable = CommissionPayable::where('university', $university)
                ->whereNull('product')
                ->whereNull('partner')
                ->first();
        }

        return $commissionPayable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.Finance.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'university' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'commission' => 'required|string|max:255',
            'promotional' => 'required|string|max:255',
            'tuition' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
        ]);

        Finance::create($request->all());

        return redirect()->route('backend.Finance.index')->with('success', 'Finance saved successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $finances = Finance::findOrFail($id);
        return view('backend.Finance.update', compact('finances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finances = Finance::findOrFail($id);

        $request->validate([
            'university' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'commission' => 'required|string|max:255',
            'promotional' => 'required|string|max:255',
            'tuition' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
        ]);

        $finances->update($request->all());

        return redirect()->route('backend.Finance.index')->with('success', 'Finance updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $finances = Finance::findOrFail($id);
        $finances->delete();

        return redirect()->route('backend.Finance.index')->with('success', 'Finance deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            DB::beginTransaction();

            foreach ($rows as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                if (count($row) < 6) {
                    throw new \Exception('Invalid row format. Expected 6 columns.');
                }

                Finance::create([
                    'university' => trim($row[0]) ?? null,
                    'country' => trim($row[1]) ?? null,
                    'commission' => trim($row[2]) ?? null,
                    'promotional' => trim($row[3]) ?? null,
                    'tuition' => trim($row[4]) ?? null,
                    'comment' => trim($row[5]) ?? null,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error importing data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function visa()
    {
        $applications = Application::all();
        $finances = Finance::all();
        return view('backend.Finance.visa', compact('applications', 'finances'));
    }

    public function accountreceivable()
    {
        $applications = Application::with('store')->get();
        $finances = Finance::all();
        $stores = Store::all();
        $data_entries = DataEntry::all();
        $comissions = Comission::all();
        $commissionTypes = Comission::all();

        return view('backend.Finance.accountreceivable', compact('applications', 'commissionTypes', 'finances', 'stores', 'comissions', 'data_entries'));
    }

    public function accountreceivableview($id)
    {
        Log::info("Accessing accountreceivableview with Application ID: {$id}");

        $application = Application::with('store')->find($id);
       
        if (!$application) {
            Log::warning("Application with ID {$id} not found.");
            return redirect()->back()->with('error', 'Application not found.');
        }

        $applications = collect([$application]);
        $finances = Finance::all();
        $comissions = Comission::all();
        
        $stores = Store::all();
        $commissionTransactions = CommissionTransaction::all();

        return view('backend.Finance.accountreceivableview', [
            'applications' => $applications,
            'finances' => $finances,
            'comissions' => $comissions,
            
            'stores' => $stores,
            'commissionTransactions' => $commissionTransactions,
            'finances_count' => $finances->count(),
            'stores_count' => $stores->count(),
        ]);
    }

    public function accountpayableview($id)
    {
        Log::info("Accessing accountpayableview with Application ID: {$id}");

        // Eager load necessary relationships
        $application = Application::with(['store', 'commission_transaction', 'commission_transactions'])->find($id);

        if (!$application) {
            Log::warning("Application with ID {$id} not found.");
            return redirect()->back()->with('error', 'Application not found.');
        }

        $commissionTransaction = null;

        // Method 1: Try to get from direct relationship with null safety
        if (isset($application->commission_transaction) && 
            $application->commission_transaction && 
            $application->commission_transaction->type === 'payable') {
            $commissionTransaction = $application->commission_transaction;
        }
        // Method 2: Try to get from collection relationship with null safety
        elseif (isset($application->commission_transactions) && $application->commission_transactions) {
            $commissionTransaction = $application->commission_transactions->where('type', 'payable')->first();
        }

        if ($commissionTransaction) {
            Log::info("Commission Transaction found", [
                'commission_transaction_id' => $commissionTransaction->id,
                'commissionpayable_id' => $commissionTransaction->commissionpayable_id ?? 'null',
                'paid' => $commissionTransaction->paid ?? 0,
                'exchange_rate' => $commissionTransaction->exchange_rate ?? 1,
            ]);

            if (is_null($commissionTransaction->commissionpayable_id)) {
                Log::warning("CommissionPayable ID is null for Commission Transaction ID: {$commissionTransaction->id}");
            } else {
                $commissionPayableId = $commissionTransaction->commissionpayable_id;
                $commissionPayableExists = CommissionPayable::where('id', $commissionPayableId)->exists();

                if (!$commissionPayableExists) {
                    Log::warning("CommissionPayable ID {$commissionPayableId} does not exist.");
                }
            }
        } else {
            Log::warning("No payable Commission Transaction found for Application ID: {$id}");
        }

        // Fetch related data
        $applications = collect([$application]);
        $finances = Finance::all();
        $comissions = Comission::all();
        $stores = Store::all();
        $commissionTransactions = CommissionTransaction::all();
        $comission_payable = CommissionPayable::all();

        // Pass data to the view
        return view('backend.Finance.accountpayableview', [
            'applications' => $applications,
            'finances' => $finances,
            'comissions' => $comissions,
            'stores' => $stores,
            'comission_payable' => $comission_payable,
            'commissionTransactions' => $commissionTransactions,
            'finances_count' => $finances->count(),
            'stores_count' => $stores->count(),
        ]);
    }

    public function payable()
    {
        $applications = Application::with('store')->get();
        $finances = Finance::all();
        $stores = Store::all();
        return view('backend.Finance.paypal', compact('applications', 'finances', 'stores'));
    }

    public function updateReceivable($appId, Request $request) {
        $validated = $request->validate([
            'field' => 'required|in:exchange_rate,paid',
            'value' => 'required|numeric',
            'type' => 'required|in:payable,receivable'
        ]);

        try {
            DB::beginTransaction();

            $application = Application::findOrFail($appId);
            
            // Find existing transaction with matching type
            $transaction = $application->commission_transactions()
                ->where('type', $validated['type'])
                ->first();
                
            if (!$transaction) {
                // Find the correct commission payable record
                $commissionPayable = $this->findCommissionPayable($application);
                    
                if (!$commissionPayable) {
                    throw new \Exception('No commission payable found for this application');
                }
                    
                // Create new transaction since none exists with the requested type
                $transaction = new CommissionTransaction();
                $transaction->application_id = $appId;
                $transaction->commissionpayable_id = $commissionPayable->id;
                $transaction->user_id = auth()->id();
                $transaction->type = $validated['type'];
                $transaction->status = 'pending';
                    
                if (empty($transaction->amount)) {
                    $transaction->amount = $this->calculateCommissionAmount($application);
                }
            }

            // Update the specified field
            $transaction->{$validated['field']} = $validated['value'];
            $transaction->save();

            DB::commit();

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
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Receivable update failed for app {$appId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function calculateCommissionAmount($application)
    {
        // Add null safety check
        $fee = $application->fee ?? 0;
        return $fee * 0.1; // 10% commission example
    }
}