<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Facades\Excel; // Commented out - will use alternative approach

class PartnerApiController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->paginate(10);
        return response()->json($partners);
    }


public function store(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners',
            'contact_no' => 'required|int',
            'agency_counselor_email' => 'required|email',
            'Address' => 'required|string|max:255'
        ]);

        // Check if partner with same agency name and Address exists
        $existingPartner = Partner::where('agency_name', $request->agency_name)
                                 ->where('Address', $request->Address)
                                 ->first();

        if ($existingPartner) {
            // Update existing partner
            $existingPartner->update($request->all());
            return redirect()->route('backend.partners.index')
                           ->with('success', 'Partner updated successfully (existing record found with same name and Address).');
        }

        // Create new partner if no duplicate found
        Partner::create($request->all());

    }



    /**
     * Read Excel file without Laravel Excel package
     */
    private function readExcelFile($file)
    {
        // Check if PhpSpreadsheet is available
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('PhpSpreadsheet library is not installed. Please install it or use CSV files.');
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            // Remove empty rows
            $data = array_filter($data, function($row) {
                return !empty(array_filter($row, function($cell) {
                    return !empty(trim($cell));
                }));
            });
            
            return array_values($data); // Re-index array
        } catch (\Exception $e) {
            throw new \Exception('Failed to read Excel file: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $partner = Partner::findOrFail($id);
        return response()->json($partner);
    }

 

public function bulkUpload(Request $request)
{
    // Enhanced validation with better error messages
    try {
        // Log incoming request details
        \Log::info('Bulk upload request received', [
            'has_file' => $request->hasFile('file'),
            'files_count' => count($request->allFiles()),
            'content_type' => $request->header('Content-Type'),
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded',
                'errors' => ['file' => ['No file was uploaded']]
            ], 422);
        }

        $file = $request->file('file');
        
        // Log file details
        \Log::info('File details', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'is_valid' => $file->isValid(),
            'error' => $file->getError(),
            'path' => $file->getPathname(),
        ]);

        // Check if file upload was successful
        if (!$file->isValid()) {
            $uploadError = $file->getError();
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File too large (exceeds php.ini upload_max_filesize)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form MAX_FILE_SIZE)',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            ];
            
            $message = $errorMessages[$uploadError] ?? 'Unknown upload error';
            return response()->json([
                'message' => $message,
                'errors' => ['file' => [$message]]
            ], 422);
        }

        // Enhanced MIME type validation
        $allowedMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-excel', // .xls
            'application/msexcel',
            'application/x-msexcel',
            'application/x-ms-excel',
            'application/x-excel',
            'application/x-dos_ms_excel',
            'application/xls',
            'application/xlsx',
            'text/csv',
            'application/csv',
            'text/comma-separated-values',
            'text/x-comma-separated-values',
            'text/plain', // CSV files sometimes detected as plain text
            'application/octet-stream' // Binary files
        ];

        $allowedExtensions = ['csv', 'xlsx', 'xls'];
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $fileMimeType = $file->getMimeType();

        // Check extension first
        if (!in_array($fileExtension, $allowedExtensions)) {
            return response()->json([
                'message' => "Invalid file extension: .{$fileExtension}. Allowed: " . implode(', ', $allowedExtensions),
                'errors' => ['file' => ["Invalid file extension: .{$fileExtension}"]]
            ], 422);
        }

        // For CSV files, be more lenient with MIME type
        if ($fileExtension === 'csv') {
            $csvMimeTypes = ['text/csv', 'application/csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/plain'];
            if (!in_array($fileMimeType, $csvMimeTypes)) {
                \Log::warning("CSV file has unexpected MIME type: {$fileMimeType}, but proceeding anyway");
            }
        } else {
            // For Excel files, check MIME type more strictly
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                return response()->json([
                    'message' => "Invalid file type. Expected: Excel or CSV file. Got MIME type: {$fileMimeType}",
                    'errors' => ['file' => ["Invalid MIME type: {$fileMimeType}"]]
                ], 422);
            }
        }

        // Check file size (10MB limit)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            return response()->json([
                'message' => 'File too large. Maximum size: 10MB',
                'errors' => ['file' => ['File too large. Maximum size: 10MB']]
            ], 422);
        }

        // If we get here, the file passed all validations
        \Log::info('File validation passed, starting processing');

        $extension = $file->getClientOriginalExtension();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            // Handle Excel files using PhpSpreadsheet directly
            try {
                $data = $this->readExcelFile($file);
                
                if (empty($data)) {
                    return response()->json(['message' => 'File is empty or invalid'], 400);
                }
            } catch (\Exception $e) {
                \Log::error('Excel file reading failed', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to read Excel file: ' . $e->getMessage()], 400);
            }
            
            $rows = $data;
            $headers = array_shift($rows); // Remove header row
            
            // Validate headers - normalize both expected and actual headers
            $expectedHeaders = ['agency_name', 'email', 'contact_no', 'agency_counselor_email', 'address'];
            $normalizedHeaders = array_map(function($header) {
                return trim(strtolower(str_replace(' ', '_', $header)));
            }, $headers);
            
            // More flexible header validation
            $headerMap = [];
            foreach ($expectedHeaders as $expected) {
                $found = false;
                foreach ($normalizedHeaders as $index => $normalized) {
                    if ($normalized === $expected || 
                        $normalized === str_replace('_', '', $expected) ||
                        ($expected === 'address' && in_array($normalized, ['address', 'addresses']))) {
                        $headerMap[$expected] = $index;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    return response()->json([
                        'message' => "Missing required header: '{$expected}'. Found headers: " . implode(', ', $headers)
                    ], 400);
                }
            }

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and array is 0-indexed
                
                // Skip empty rows
                if (empty(array_filter($row, function($cell) { return !empty(trim($cell)); }))) {
                    continue;
                }

                // Map data using header positions
                $partnerData = [
                    'agency_name' => $this->cleanString($row[$headerMap['agency_name']] ?? ''),
                    'email' => $this->cleanString($row[$headerMap['email']] ?? ''),
                    'contact_no' => $this->cleanString($row[$headerMap['contact_no']] ?? ''),
                    'agency_counselor_email' => $this->cleanString($row[$headerMap['agency_counselor_email']] ?? ''),
                    'Address' => $this->cleanString($row[$headerMap['address']] ?? ''),
                ];

                $validation = $this->validatePartnerData($partnerData, $rowNumber);
                if (!$validation['valid']) {
                    $errors[] = $validation['error'];
                    $skipped++;
                    continue;
                }

                // Process the partner
                $result = $this->processPartner($partnerData);
                if ($result === 'created') {
                    $created++;
                } elseif ($result === 'updated') {
                    $updated++;
                }
            }
        } else {
            // Handle CSV files
            $content = file_get_contents($file->getPathname());
            
            // Detect and convert encoding
            $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            
            // Create temporary file with cleaned content
            $tempFilePath = tempnam(sys_get_temp_dir(), 'csv_import');
            file_put_contents($tempFilePath, $content);

            if (($handle = fopen($tempFilePath, "r")) !== FALSE) {
                // Read and validate headers
                $headers = fgetcsv($handle);
                if (!$headers) {
                    fclose($handle);
                    unlink($tempFilePath);
                    return response()->json(['message' => 'Invalid CSV file'], 400);
                }

                // Normalize headers for flexible matching
                $expectedHeaders = ['agency_name', 'email', 'contact_no', 'agency_counselor_email', 'address'];
                $normalizedHeaders = array_map(function($header) {
                    return trim(strtolower(str_replace(' ', '_', $header)));
                }, $headers);
                
                // Create header mapping
                $headerMap = [];
                foreach ($expectedHeaders as $expected) {
                    $found = false;
                    foreach ($normalizedHeaders as $index => $normalized) {
                        if ($normalized === $expected || 
                            $normalized === str_replace('_', '', $expected) ||
                            ($expected === 'address' && in_array($normalized, ['Address', 'Addresses']))) {
                            $headerMap[$expected] = $index;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        fclose($handle);
                        unlink($tempFilePath);
                        return response()->json([
                            'message' => "Missing required header: '{$expected}'. Found headers: " . implode(', ', $headers)
                        ], 400);
                    }
                }

                $rowNumber = 1;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $rowNumber++;
                    
                    // Skip empty rows
                    if (empty(array_filter($row, function($cell) { return !empty(trim($cell)); }))) {
                        continue;
                    }

                    // Map data using header positions
                    $partnerData = [
                        'agency_name' => $this->cleanString($row[$headerMap['agency_name']] ?? ''),
                        'email' => $this->cleanString($row[$headerMap['email']] ?? ''),
                        'contact_no' => $this->cleanString($row[$headerMap['contact_no']] ?? ''),
                        'agency_counselor_email' => $this->cleanString($row[$headerMap['agency_counselor_email']] ?? ''),
                        'Address' => $this->cleanString($row[$headerMap['address']] ?? ''),
                    ];

                    $validation = $this->validatePartnerData($partnerData, $rowNumber);
                    if (!$validation['valid']) {
                        $errors[] = $validation['error'];
                        $skipped++;
                        continue;
                    }

                    // Process the partner
                    $result = $this->processPartner($partnerData);
                    if ($result === 'created') {
                        $created++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    }
                }
                fclose($handle);
                unlink($tempFilePath);
            }
        }

        $message = "Upload completed! Created: {$created}, Updated: {$updated}, Skipped: {$skipped}";
        
        if (!empty($errors) && count($errors) <= 10) {
            $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $message .= "\n... and " . (count($errors) - 10) . " more errors";
            }
        }

        return response()->json([
            'message' => $message,
            'stats' => [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'errors' => count($errors)
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Bulk upload exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }

}
    private function cleanString($value)
    {
        if (!is_string($value)) {
            $value = (string) $value;
        }
        
        // Remove BOM if present
        $value = preg_replace('/^\x{FEFF}/u', '', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        // Remove any remaining non-printable characters except newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        return $value;
    }

    private function validatePartnerData($data, $rowNumber)
    {
        $required = ['agency_name', 'email', 'contact_no', 'agency_counselor_email', 'Address']; // Fixed key name
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'error' => "Row {$rowNumber}: Missing required field '{$field}'"
                ];
            }
        }

        // Validate email formats
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => "Row {$rowNumber}: Invalid email format for 'email' field"
            ];
        }

        if (!filter_var($data['agency_counselor_email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => "Row {$rowNumber}: Invalid email format for 'agency_counselor_email' field"
            ];
        }

        return ['valid' => true];
    }

    private function processPartner($partnerData)
    {
        // Check if partner exists (by agency_name and Address)
        $existingPartner = Partner::where('agency_name', $partnerData['agency_name'])
            ->where('Address', $partnerData['Address']) // Fixed column name
            ->first();

        if ($existingPartner) {
            $existingPartner->update($partnerData);
            return 'updated';
        } else {
            Partner::create($partnerData);
            return 'created';
        }
    }

    public function downloadTemplate(Request $request)
    {
        $format = $request->get('format', 'csv');

        $headers = [
            'agency_name',
            'email',
            'contact_no',
            'agency_counselor_email',
            'Address'
        ];

        $sampleData = [
            ['Sample Agency', 'sample@agency.com', '1234567890', 'counselor@agency.com', '123 Sample St']
        ];

        if ($format === 'xlsx') {
            // Try to create Excel file if PhpSpreadsheet is available
            if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                try {
                    return $this->createExcelTemplate($headers, $sampleData);
                } catch (\Exception $e) {
                    // Fall back to CSV if Excel creation fails
                    return $this->createCsvTemplate($headers, $sampleData);
                }
            } else {
                // PhpSpreadsheet not available, return CSV instead
                return $this->createCsvTemplate($headers, $sampleData);
            }
        } else {
            return $this->createCsvTemplate($headers, $sampleData);
        }
    }

    private function createExcelTemplate($headers, $sampleData)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->fromArray($headers, null, 'A1');
        
        // Set sample data
        $sheet->fromArray($sampleData, null, 'A2');
        
        // Style headers
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, 'partners_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function createCsvTemplate($headers, $sampleData)
    {
        $csvHeaders = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="partners_template.csv"',
        ];

        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $csvHeaders);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agency_name' => 'required|string|max:255',
            'Address' => 'required|string|max:255', // Fixed field name
            'email' => 'required|email|max:255',
            'contact_no' => 'required|string|max:255',
            'agency_counselor_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $partner->update($request->all());

        return response()->json(['message' => 'Partner updated successfully.', 'partner' => $partner]);
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();
        return response()->json(['message' => 'Partner deleted successfully.']);
    }
}