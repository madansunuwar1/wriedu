<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Store;
use App\Models\CommissionPayable;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ComissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $comissions = Comission::all();
            return view('backend.comission.index', compact('comissions'));
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve commissions.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('backend.comission.create');
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'university' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'partner' => 'nullable|string|max:255',
            'has_progressive_commission' => 'required|string|in:yes,no',
            'progressive_commission' => 'nullable|string|max:255',
            'has_bonus_commission' => 'required|string|in:yes,no',
            'bonus_commission' => 'nullable|string|max:255',
            'has_intensive_commission' => 'required|string|in:yes,no',
            'intensive_commission' => 'nullable|string|max:255',
            'commissionTypes' => 'required|array|min:1',
            'commissionValues' => 'required|array|min:1',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        // Convert radio button values to boolean
        $hasProgressiveCommission = $request->has_progressive_commission === 'yes';
        $hasBonusCommission = $request->has_bonus_commission === 'yes';
        $hasIntensiveCommission = $request->has_intensive_commission === 'yes';

        // Build commission types array
        $commissionData = [];
        foreach ($request->commissionTypes as $type) {
            if (isset($request->commissionValues[$type])) {
                $commissionData[$type] = $request->commissionValues[$type];
            }
        }

        try {
            Comission::create([
                'university' => $request->university,
                'product' => $request->product,
                'partner' => $request->partner,
                'has_progressive_commission' => $hasProgressiveCommission,
                'progressive_commission' => $hasProgressiveCommission ? $request->progressive_commission : null,
                'has_bonus_commission' => $hasBonusCommission,
                'bonus_commission' => $hasBonusCommission ? $request->bonus_commission : null,
                'has_intensive_commission' => $hasIntensiveCommission,
                'intensive_commission' => $hasIntensiveCommission ? $request->intensive_commission : null,
                'commission_types' => $commissionData,
                'store_id' => $request->store_id,
            ]);

            return redirect()->route('backend.comission.index')
                ->with('success', 'Commission rate added successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating Commission: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create commission rate: ' . $e->getMessage());
        }
    }
    public function commissionindex()
{
    try {
        Log::info('Fetching commission payable data...');
        $commission_payable = CommissionPayable::all();
        Log::info('Commission payable data fetched successfully.');

        Log::info('Fetching regular commission data...');
        $comissions = Comission::all();
        Log::info('Regular commission data fetched successfully.');

        return view('backend.Finance.commissionindex', compact('commission_payable', 'comissions'));
    } catch (\Exception $e) {
        Log::error('Failed to load commission data: ' . $e->getMessage(), [
            'stack' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to load commission data: ' . $e->getMessage());
    }
}

 public function duplicate(Request $request, $id) // Added Request
    {
        try {
            $commissionPayable = CommissionPayable::findOrFail($id);
            $newCommissionPayable = $commissionPayable->replicate();
            $newCommissionPayable->save();

            return response()->json(['message' => 'Commission Payable record duplicated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error duplicating CommissionPayable: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to duplicate Commission Payable record.'], 500);
        }
    }

    public function markAsPaid(Request $request, $id) // Added Request
    {
        try {
            $commissionPayable = CommissionPayable::findOrFail($id);
            $commissionPayable->paid = true; // Assuming you have a 'paid' field
            $commissionPayable->save();

            return response()->json(['message' => 'Commission Payable record marked as paid.']);
        } catch (\Exception $e) {
            Log::error('Error marking CommissionPayable as paid: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to mark Commission Payable record as paid.'], 500);
        }
    }

    public function destroy(Request $request, $id) // Added Request
    {
        try {
            $commissionPayable = CommissionPayable::findOrFail($id);
            $commissionPayable->delete();

            return response()->json(['message' => 'Commission Payable record deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting CommissionPayable: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete Commission Payable record.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Comission $comission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $comissions = Comission::findOrFail($id);
            $commissionsData = $comissions->commission_types ?? [];

            return view('backend.comission.updateCommission', compact('comissions', 'commissionsData'));
        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified commission in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $field_type = $request->field_type;
            Log::info('Update request received', [
                'application_id' => $id,
                'field_type' => $field_type,
                'request_data' => $request->all()
            ]);

            // Find the application
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Application not found.'
                ], 404);
            }

            // Find or create a store record based on the application ID
            $store = Store::firstOrCreate(['application_id' => $application->id]);

            // Handle exchange rate updates for Store model
            if ($field_type === 'exchange') {
                $store->exchange = $request->value;
                $store->save();

                Log::info('Store exchange updated', [
                    'store_id' => $store->id,
                    'new_exchange' => $request->value
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Exchange rate updated successfully'
                ]);
            }

            // Find or create the commission record
            $commission = Comission::where('applications_id', $application->id)->first();

            if (!$commission) {
                // Create new commission record
                $emptyJson = json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $commission = Comission::create([
                    'store_id' => $store->id,
                    'applications_id' => $application->id,
                    'university' => $application->university ?? null,
                    'product' => $application->english ?? null,
                    'commission_types' => []
                ]);

                Log::info('New commission record created', [
                    'commission_id' => $commission->id,
                    'store_id' => $store->id,
                    'application_id' => $application->id
                ]);
            } else {
                // Update existing commission to link with store if not already linked
                if (!$commission->store_id) {
                    $commission->store_id = $store->id;
                    $commission->save();
                }
            }

            // Handle commission data updates
            if ($field_type === 'commission_type' || $field_type === 'commission') {
                $commissionTypes = $this->getCommissionTypesAsArray($commission->commission_types);

                if ($field_type === 'commission_type') {
                    $field = $request->field;
                    $value = $request->value;

                    if (is_string($value)) {
                        $value = $this->removeForwardSlashes($value);
                    }

                    $standardizedField = ucfirst(strtolower($field));
                    $commissionTypes[$standardizedField] = $value;
                } elseif ($field_type === 'commission') {
                    $value = $request->commission_value;

                    if (is_string($value)) {
                        $value = $this->removeForwardSlashes($value);
                    }

                    $commissionTypes['Net'] = $value;
                }

                // Update commission_types using model update
                $commission->update(['commission_types' => $commissionTypes]);

                Log::info('Commission data updated', [
                    'commission_id' => $commission->id,
                    'application_id' => $application->id,
                    'commission_types' => $commissionTypes
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Commission data updated successfully'
                ]);
            }

            // Handle the 'paid' field
            if ($field_type === 'paid') {
                $commission->update([
                    'paid' => $request->value,
                    'applications_id' => $application->id
                ]);

                Log::info('Paid value updated', [
                    'commission_id' => $commission->id,
                    'application_id' => $application->id,
                    'paid' => $request->value
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Paid value updated successfully'
                ]);
            }

            // Handle other fields
            if (in_array($field_type, ['other_field1', 'other_field2', 'other', 'bonus', 'incentive'])) {
                $field = $request->field;
                $value = $request->value;

                if (is_string($value)) {
                    $value = $this->removeForwardSlashes($value);
                }

                // Check if the field exists on the Commission model
                if (in_array($field, (new Comission)->getFillable())) {
                    $commission->update([
                        $field => $value,
                        'applications_id' => $application->id
                    ]);

                    Log::info('Other commission field updated', [
                        'commission_id' => $commission->id,
                        'application_id' => $application->id,
                        'field' => $field,
                        'value' => $value
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data updated successfully'
                    ]);
                } else {
                    // Handle fields that should be stored in commission_types JSON
                    $commissionTypes = $this->getCommissionTypesAsArray($commission->commission_types);
                    $standardizedField = ucfirst(strtolower($field));
                    $commissionTypes[$standardizedField] = $value;

                    $commission->update(['commission_types' => $commissionTypes]);

                    Log::info('Other commission type updated', [
                        'commission_id' => $commission->id,
                        'application_id' => $application->id,
                        'field' => $standardizedField,
                        'value' => $value
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data updated successfully'
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field type'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Failed to update data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'application_id' => $id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to safely get commission_types as an array.
     */
    private function getCommissionTypesAsArray($commissionTypes)
    {
        if (empty($commissionTypes)) {
            return [];
        }

        if (is_string($commissionTypes)) {
            $decoded = json_decode($commissionTypes, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (is_array($commissionTypes)) {
            return $commissionTypes;
        }

        return [];
    }

    /**
     * Helper function to remove forward slashes from a string
     */
    private function removeForwardSlashes($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return str_replace('/', '', $value);
    }

    public function updateCommission(Request $request, $id)
    {
        
        try {
            $data = $request->all();

            Log::info('Single field update request received', [
                'commission_id' => $id,
                'request_data' => $data
            ]);

            // Validation
            $validator = Validator::make($data, [
                'university' => 'nullable|string',
                'product' => 'nullable|string',
                'commission_type' => 'nullable|string',
                'commission_value' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Use find instead of findOrFail to handle the error more gracefully
            $commission = Comission::find($id);
            
            if (!$commission) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Commission record not found'
                ], 404);
            }

            // Get existing commission_types as an array
            $commissionTypes = $this->getCommissionTypesAsArray($commission->commission_types);

            Log::info('Existing commission_types', ['commission_types' => $commissionTypes]);

            $updated = false;
            $updateData = [];

            // Process updates
            if (isset($data['university'])) {
                $updateData['university'] = $this->removeForwardSlashes($data['university']);
                $updated = true;
            }

            if (isset($data['product'])) {
                $updateData['product'] = $this->removeForwardSlashes($data['product']);
                $updated = true;
            }

            $commissionTypeFields = ['enrollment', 'visa', 'accommodation', 'pickup', 'insurance', 'net', 'bonus', 'standard', 'intensive'];

            foreach ($commissionTypeFields as $fieldKey) {
                if (array_key_exists($fieldKey, $data)) {
                    $standardizedField = ucfirst($fieldKey);

                    if ($data[$fieldKey] === null || $data[$fieldKey] === '') {
                        unset($commissionTypes[$standardizedField]);
                    } else {
                        $value = is_string($data[$fieldKey]) ? $this->removeForwardSlashes($data[$fieldKey]) : $data[$fieldKey];
                        $commissionTypes[$standardizedField] = $value;
                    }
                    $updated = true;
                }
            }

            // Handle commission_type and commission_value combo
            if (isset($data['commission_type']) && isset($data['commission_value'])) {
                $typeField = ucfirst($data['commission_type']);

                if ($data['commission_value'] === null || $data['commission_value'] === '') {
                    unset($commissionTypes[$typeField]);
                } else {
                    $value = is_string($data['commission_value']) ? $this->removeForwardSlashes($data['commission_value']) : $data['commission_value'];
                    $commissionTypes[$typeField] = $value;
                }
                $updated = true;
            }

            // Save the updated data
            if ($updated) {
                if (!empty($updateData)) {
                    $updateData['commission_types'] = $commissionTypes;
                } else {
                    $updateData = ['commission_types' => $commissionTypes];
                }

                $commission->update($updateData);

                Log::info('Commission updated successfully', [
                    'commission_id' => $id,
                    'updated_data' => $updateData
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Commission updated successfully'
                ]);
            }

            Log::warning('No valid fields found for update', [
                'commission_id' => $id,
                'request_data' => $data
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'No valid fields provided for update'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Failed to update commission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'commission_id' => $id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function record($id)
    {
        try {
            $comissions = Comission::find($id);

            if (!$comissions) {
                return redirect()->back()->with('error', 'Commission record not found.');
            }

            return view('backend.comission.record', compact('comissions'));
        } catch (\Exception $e) {
            Log::error('Error in record method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve commission record.');
        }
    }

    public function indexs(Request $request)
    {
        $view = $request->input('view', 'receivable');

        $applications = collect();
        $comissions = collect();
        $commission_payable = collect();

        if ($view === 'receivable') {
            $applications = Application::with('commissionTransactions')->get();
            $comissions = CommissionPayable::all();
        } elseif ($view === 'payable') {
            $commission_payable = CommissionPayable::all();
        } else {
            $view = 'receivable';
            $applications = Application::with('commissionTransactions')->get();
            $comissions = CommissionPayable::all();
        }

        return view('backend.Finance.indexs', compact('view', 'applications', 'comissions', 'commission_payable'));
    }

    

     public function duplicateCommission($id)
    {
        try {
            $originalCommission = Comission::findOrFail($id);
            
            $duplicatedData = $originalCommission->toArray();
            unset($duplicatedData['id']);
            unset($duplicatedData['created_at']);
            unset($duplicatedData['updated_at']);
            
            // Add "(Copy)" to distinguish the duplicate
            $duplicatedData['university'] = $originalCommission->university . ' (Copy)';
            
            $newCommission = Comission::create($duplicatedData);

            return response()->json([
                'success' => true,
                'message' => 'Commission record duplicated successfully.',
                'data' => $newCommission
            ]);
        } catch (Exception $e) {
            Log::error('Commission duplicate error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate commission record.'
            ], 500);
        }
    }


     public function destroyCommission($id)
    {
        try {
            $commission = Commission::findOrFail($id);
            $commission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commission record deleted successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Commission delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete commission record.'
            ], 500);
        }
    }


    public function exportCommission()
    {
        try {
            $commissions = Commission::all();
            
            $filename = 'commission_records_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($commissions) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID', 'University', 'Product', 'Partner', 
                    'Commission Types', 'Bonus Commission', 'Intensive Commission',
                    'Created At', 'Updated At'
                ]);

                // Add data rows
                foreach ($commissions as $commission) {
                    fputcsv($file, [
                        $commission->id,
                        $commission->university,
                        $commission->product,
                        $commission->partner,
                        is_array($commission->commission_types) 
                            ? json_encode($commission->commission_types) 
                            : $commission->commission_types,
                        $commission->bonus_commission,
                        $commission->intensive_commission,
                        $commission->created_at,
                        $commission->updated_at,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('Commission export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export commission data.');
        }
    }

     public function importCommission(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048'
            ]);

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            // Remove header row
            $header = array_shift($data);
            
            $imported = 0;
            foreach ($data as $row) {
                if (count($row) >= 6) {
                    Commission::create([
                        'university' => $row[0] ?? '',
                        'product' => $row[1] ?? '',
                        'partner' => $row[2] ?? '',
                        'commission_types' => $row[3] ?? null,
                        'bonus_commission' => is_numeric($row[4]) ? $row[4] : 0,
                        'intensive_commission' => is_numeric($row[5]) ? $row[5] : 0,
                    ]);
                    $imported++;
                }
            }

            return redirect()->back()->with('success', "Successfully imported {$imported} commission records.");
        } catch (Exception $e) {
            Log::error('Commission import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import commission data.');
        }
    }

}