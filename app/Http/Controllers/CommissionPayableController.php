<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Store;
use App\Models\Application;
use App\Models\Partner;
use App\Models\Comission;
use App\Models\CommissionPayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionPayableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        $applications = Application::with('store')->get();
        $finances = Finance::all();
        $stores = Store::all();
        $comissions = Comission::all();
        $commissionTypes = Comission::all();
                $partners = Partner::all();
         // Adjust the model name if it's different

        return view('backend.comission.payable.comission', compact('applications', 'commissionTypes', 'finances', 'stores', 'comissions','partners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function index()
    {
        $commission_payable = CommissionPayable::all(); // Example: get all records

        return view('backend.comission.payable.index', compact('commission_payable'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'university' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'partner' => 'required|string|max:255',
            'has_progressive_commission' => 'required|string|in:yes,no',
            'progressive_commission' => 'nullable|string|max:255',
            'has_bonus_commission' => 'required|string|in:yes,no',
            'bonus_commission' => 'nullable|string|max:255',
            'has_incentive_commission' => 'required|string|in:yes,no',
            'incentive_commission' => 'nullable|string|max:255',
            'commissionTypes' => 'required|array|min:1',
            'commissionValues' => 'required|array|min:1',
        ]);
    
        // Convert radio button values to boolean
        $hasProgressiveCommission = $request->has_progressive_commission === 'yes';
        $hasBonusCommission = $request->has_bonus_commission === 'yes';
        $hasincentiveCommission = $request->has_incentive_commission === 'yes';
    
        // Build commission types array
        $commissionData = [];
        foreach ($request->commissionTypes as $type) {
            if (isset($request->commissionValues[$type])) {
                $commissionData[$type] = $request->commissionValues[$type];
            }
        }
    
        try {
            CommissionPayable::create([
                'university' => $request->university,
                'product' => $request->product,
                'partner' => $request->partner,
                'has_progressive_commission' => $hasProgressiveCommission,
                'progressive_commission' => $hasProgressiveCommission ? $request->progressive_commission : null,
                'has_bonus_commission' => $hasBonusCommission,
                'bonus_commission' => $hasBonusCommission ? $request->bonus_commission : null,
                'has_incentive_commission' => $hasincentiveCommission,
                'incentive_commission' => $hasincentiveCommission ? $request->incentive_commission : null,
                'commission_types' => $commissionData,
            ]);
    
            return redirect()->route('backend.comission.payable.index')
                ->with('success', 'Commission rate added successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating CommissionPayable: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e,
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create commission rate: ' . $e->getMessage());
        }
    } 

    public function record($id)
    {
        try {
            Log::info('Attempting to fetch Commission record with ID: ' . $id);
            $commission_payable = CommissionPayable::findOrFail($id);

            return view('backend.comission.payable.record', compact('commission_payable'));

        } catch (\Exception $e) {
            Log::error('Error fetching Commission Record:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(404, 'Commission record not found.');
        }
    }

    /**
 * Update commission details
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function updateCommission(Request $request, $id)
{
    try {
        $commission = CommissionPayable::findOrFail($id);
        
        // Handle commission types update
        if ($request->has('commission_type') && $request->has('commission_value')) {
            $commissionType = $request->commission_type;
            $commissionValue = $request->commission_value;
            
            // Initialize commission_types as array if it's not already
            $commissionTypes = is_array($commission->commission_types) 
                ? $commission->commission_types 
                : [];
            
            // Update specific commission type
            $commissionTypes[$commissionType] = $commissionValue;
            
            // Save updated commission types
            $commission->commission_types = $commissionTypes;
            $commission->save();
            
            return response()->json([
                'success' => true,
                'message' => "$commissionType commission updated successfully"
            ]);
        }
        
        // Handle boolean fields for has_bonus_commission and has_incentive_commission
        if ($request->has('has_bonus_commission')) {
            $commission->has_bonus_commission = (bool)$request->has_bonus_commission;
            
            // If set to false (0), clear the bonus_commission value
            if (!$commission->has_bonus_commission && $request->filled('bonus_commission')) {
                $commission->bonus_commission = null;
            } elseif ($request->filled('bonus_commission')) {
                $commission->bonus_commission = $request->bonus_commission;
            }
        }
        
        if ($request->has('has_incentive_commission')) {
            $commission->has_incentive_commission = (bool)$request->has_incentive_commission;
            
            // If set to false (0), clear the incentive_commission value
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
        
        // Handle regular field updates
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
            'message' => 'Commission details updated successfully'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Commission update error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to update commission details: ' . $e->getMessage()
        ], 500);
    }
}

  public function duplicatePayable($id)
    {
        try {
            $originalCommission = CommissionPayable::findOrFail($id);
            
            $duplicatedData = $originalCommission->toArray();
            unset($duplicatedData['id']);
            unset($duplicatedData['created_at']);
            unset($duplicatedData['updated_at']);
            
            // Add "(Copy)" to distinguish the duplicate
            $duplicatedData['university'] = $originalCommission->university . ' (Copy)';
            
            $newCommission = CommissionPayable::create($duplicatedData);

            return response()->json([
                'success' => true,
                'message' => 'Commission record duplicated successfully.',
                'data' => $newCommission
            ]);
        } catch (Exception $e) {
            Log::error('Commission payable duplicate error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate commission record.'
            ], 500);
        }
    }



     public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();
            
            $payableCommission = CommissionPayable::findOrFail($id);
            
            // Move to commission table (paid records)
            $commissionData = $payableCommission->toArray();
            unset($commissionData['id']);
            unset($commissionData['created_at']);
            unset($commissionData['updated_at']);
            
            CommissionPayable::create($commissionData);
            
            // Delete from payable table
            $payableCommission->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commission marked as paid and moved to records.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Commission mark as paid error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark commission as paid.'
            ], 500);
        }
    }

     public function destroyPayable($id)
    {
        try {
            $commission = CommissionPayable::findOrFail($id);
            $commission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commission record deleted successfully.'
            ]);
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
            
            // Remove header row
            $header = array_shift($data);
            
            $imported = 0;
            foreach ($data as $row) {
                if (count($row) >= 6) {
                    CommissionPayable::create([
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
            Log::error('Commission payable import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import commission data.');
        }
    }
}