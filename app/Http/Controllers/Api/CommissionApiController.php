<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comission as CommissionRate; // Using an alias for clarity
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class CommissionApiController extends Controller
{
    /**
     * Fetch a list of all commission rates.
     */
    public function index(Request $request)
    {
        try {
            $rates = CommissionRate::latest()->get();
            return response()->json(['success' => true, 'data' => $rates]);
        } catch (Exception $e) {
            Log::error('API Commission Rate Index Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve commission rates.'], 500);
        }
    }

    /**
     * Remove the specified commission rate from storage.
     */
    public function destroy($id)
    {
        try {
            $rate = CommissionRate::findOrFail($id);
            $rate->delete();
            return response()->json(['success' => true, 'message' => 'Commission rate deleted successfully.']);
        } catch (Exception $e) {
            Log::error('API Commission Rate Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete commission rate.'], 500);
        }
    }

    /**
     * Duplicate the specified commission rate.
     */
    public function duplicate($id)
    {
        try {
            $original = CommissionRate::findOrFail($id);
            $newRate = $original->replicate();
            $newRate->university = $original->university . ' (Copy)';
            $newRate->save();

            return response()->json([
                'success' => true,
                'message' => 'Commission rate duplicated successfully.',
                'data' => $newRate
            ]);
        } catch (Exception $e) {
            Log::error('API Commission Rate Duplicate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to duplicate commission rate.'], 500);
        }
    }

    /**
     * Handle CSV/XLSX file import for commission rates.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            // Your existing import logic can be adapted here.
            // This is a simplified example.
            $file = $request->file('file');
            // Logic to process the file (e.g., using a library like Laravel Excel)
            // ...

            return response()->json(['success' => true, 'message' => 'File imported successfully.']);
        } catch (Exception $e) {
            Log::error('API Commission Rate Import Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to import file.'], 500);
        }
    }

    /**
     * Handle data export to a CSV file.
     */
    public function export()
    {
        try {
            $rates = CommissionRate::all();
            $filename = 'commission_rates_' . date('Y-m-d') . '.csv';
            
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($rates) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'University', 'Product', 'Partner', 'Commission Types', 'Bonus', 'Intensive']);

                foreach ($rates as $rate) {
                    fputcsv($file, [
                        $rate->id,
                        $rate->university,
                        $rate->product,
                        $rate->partner,
                        json_encode($rate->commission_types), // Simple JSON representation
                        $rate->bonus_commission,
                        $rate->intensive_commission
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('API Commission Rate Export Error: ' . $e->getMessage());
            // This will return a JSON error, which is better for an API client
            return response()->json(['success' => false, 'message' => 'Failed to export data.'], 500);
        }
    }
}