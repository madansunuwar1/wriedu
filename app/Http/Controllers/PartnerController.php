<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartnersImport;


class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->paginate(10);
        return view('backend.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('backend.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners',
            'contact_no' => 'required|string|max:20',
            'agency_counselor_email' => 'required|email',
            'Address' => 'required|string|max:255'
        ]);

        // Check if partner with same agency name and address exists
        $existingPartner = Partner::where('agency_name', $request->agency_name)
                                 ->where('Address', $request->Address)
                                 ->first();

        if ($existingPartner) {
            // Update existing partner
            $existingPartner->update($request->all());
            return redirect()->route('backend.partners.index')
                           ->with('success', 'Partner updated successfully (existing record found with same name and address).');
        }

        // Create new partner if no duplicate found
        Partner::create($request->all());

        return redirect()->route('backend.partners.index')
                         ->with('success', 'Partner created successfully.');
    }

    public function show(Partner $partner)
    {
        return view('partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('backend.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'agency_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:partners,email,' . $partner->id,
            'contact_no' => 'nullable|string|max:20',
            'agency_counselor_email' => 'nullable|email',
            'Address' => 'nullable|string|max:255'
        ]);

        $partner->update($request->all());

        return redirect()->route('backend.partners.index')
                         ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('backend.partners.index')
                         ->with('success', 'Partner deleted successfully.');
    }

    public function import()
    {
        return view('backend.partners.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new PartnersImport();
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();

            return redirect()->route('backend.partners.index')
                           ->with('success', 
                               "Import completed successfully! " .
                               "Created: {$results['created']}, " .
                               "Updated: {$results['updated']}, " .
                               "Skipped: {$results['skipped']}"
                           );

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function bulkImportFromArray(array $partnersData)
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($partnersData as $index => $partnerData) {
            try {
                // Validate each row
                $validator = Validator::make($partnerData, [
                    'agency_name' => 'required|string|max:255',
                    'email' => 'required|email',
                    'contact_no' => 'required|string|max:20',
                    'agency_counselor_email' => 'required|email',
                    'Address' => 'required|string|max:255'
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    $skipped++;
                    continue;
                }

                // Check if partner with same agency name and address exists
                $existingPartner = Partner::where('agency_name', $partnerData['agency_name'])
                                         ->where('Address', $partnerData['Address'])
                                         ->first();

                if ($existingPartner) {
                    // Update existing partner
                    $existingPartner->update($partnerData);
                    $updated++;
                } else {
                    // Check if email already exists for a different partner
                    $emailExists = Partner::where('email', $partnerData['email'])
                                         ->where(function($query) use ($partnerData) {
                                             $query->where('agency_name', '!=', $partnerData['agency_name'])
                                                   ->orWhere('Address', '!=', $partnerData['Address']);
                                         })
                                         ->exists();

                    if ($emailExists) {
                        $errors[] = "Row " . ($index + 1) . ": Email already exists for a different partner";
                        $skipped++;
                        continue;
                    }

                    // Create new partner
                    Partner::create($partnerData);
                    $created++;
                }

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                $skipped++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }
    public function downloadTemplate(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $headers = [
            'agency_name',
            'email', 
            'contact_no',
            'agency_counselor_email',
            'address'
        ];
        
        
        if ($format === 'xlsx') {
            return Excel::download(new PartnersTemplateExport($headers, $sampleData), 'partners_template.xlsx');
        } else {
            // CSV format
            $csvHeaders = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="partners_template.csv"',
            ];

            $callback = function() use ($headers, $sampleData) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, $headers);
                
                // Add sample data
                foreach ($sampleData as $row) {
                    fputcsv($file, $row);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $csvHeaders);
        }
    }
}