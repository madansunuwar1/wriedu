@extends('layouts.admin') {{-- Keep your layout extension --}}

{{-- Include necessary scripts --}}
@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    {{-- SweetAlert library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Container for dynamically loaded content --}}
    {{-- IMPORTANT: This #financeContentArea element is NOT replaced by AJAX. Only its INNER HTML is updated. --}}
    {{-- Ensure switcher buttons and view-specific controls/tables are inside this div. --}}
    <div id="financeContentArea" class="searchable-container">

        {{-- SweetAlert success message - common for both views --}}
        {{-- Note: For AJAX-loaded content, you might need a different way to show these, or rely on the JS handling success --}}
        {{-- However, this handles initial page load redirect flashes. --}}
        @if(session('success'))
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-ok-button'
                    }
                });
            </script>
        @endif

        {{-- Start the main conditional block for view switching --}}
        {{-- Only the receivable view remains --}}
        @if($view === 'receivable')
            {{-- Accounts Receivable Content --}}

            {{-- Card Body for Switcher, Heading, Search, and Action Buttons for RECEIVABLE --}}
            <div class="card card-body">
                {{-- Switcher Buttons --}}
                {{-- Keep both buttons, but Payable will effectively just reload Receivable view based on backend/AJAX logic --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="btn-group" role="group" aria-label="Finance View Switcher">
                            <button type="button" class="btn btn-outline-primary @if($view === 'receivable') active @endif" data-view="receivable">
                                <i class="fas fa-chart-bar"></i> Accounts Receivable
                            </button>
                            {{-- The Commission Payable button remains but clicking it will load the 'payable' view
                                 which, in this modified Blade, results in the *same* @if($view === 'receivable') content loading
                                 if your backend renders this same Blade based on the view parameter,
                                 or if your backend redirects 'payable' to 'receivable'.
                                 The client-side JS will still attempt to load '/finance?view=payable'.
                                 The displayed content will depend on the backend response for that URL. --}}
                            <button type="button" class="btn btn-outline-primary @if($view === 'payable') active @endif" data-view="payable">
                                <i class="fas fa-money-bill-wave"></i> Commission Payable
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Row for Heading, Search, Download, Import --}}
                <div class="row">
                    <div class="col-md-12 col-xl-12 mb-3 mb-md-0"> {{-- Added margin-bottom for spacing --}}
                        <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Accounts Receivable</div>
                    </div>
                    <div class="col-md-6 col-xl-8 mb-3 mb-md-0"> {{-- Added margin-bottom for spacing --}}
                        <div class="position-relative">
                            <input type="text" class="form-control product-search ps-5" id="receivableSearchInput" placeholder="Search Receivable Data...">
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mb-3 mb-md-0"> {{-- Added margin-bottom for spacing --}}
                       <button id="receivableDownloadButton" class="btn btn-primary d-flex align-items-center">
                           <i class="ti ti-download text-white me-1 fs-5"></i> Download Data
                       </button>
                    </div>
                     <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center">
                        {{-- Button to open the Import Modal --}}
                        <button id="receivableImportButton" class="btn btn-primary d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#receivableImportModal">
                            <i class="ti ti-upload text-white me-1 fs-5"></i> Import Data
                        </button>
                     </div>
                </div>
            </div> {{-- End Card Body for Receivable --}}

            {{-- Accounts Receivable Table --}}
            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle" id="payableDataTable">
                    <thead class="text-dark fs-4">
                        <tr>
                            {{-- Filter Popups in TH - Restored --}}
                            {{-- Changed icon to fas fa-bars and added filterable-header class --}}
                            <th class="filterable-header">
                                NAME <i class="bi bi-funnel" onclick="showFilterPopup(0, event)"></i>
                                <div class="filter-popup" id="filterPopup0" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Filter Name" onkeyup="filterTable(0, this.value)">
                                </div>
                            </th>
                            <th class="filterable-header">
                                UNIVERSITY <i class="bi bi-funnel" onclick="showFilterPopup(1, event)"></i>
                                <div class="filter-popup" id="filterPopup1" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Filter University" onkeyup="filterTable(1, this.value)">
                                </div>
                            </th>
                            <th class="filterable-header">
                                PRODUCT <i class="bi bi-funnel" onclick="showFilterPopup(2, event)"></i>
                                <div class="filter-popup" id="filterPopup2" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Filter Product" onkeyup="filterTable(2, this.value)">
                                </div>
                            </th>
                             {{-- Added Filter Popup for Intake (Column index 3) --}}
                             <th class="filterable-header">
                                INTAKE <i class="bi bi-funnel" onclick="showFilterPopup(3, event)"></i>
                                <div class="filter-popup" id="filterPopup3" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Filter Intake" onkeyup="filterTable(3, this.value)">
                                </div>
                            </th>
                            <th>Total Fees Paid</th>
                            <th>Commission</th>
                            <th>Bonus (USD)</th>
                            <th>Incentive (USD)</th>
                            <th>Total (USD)</th>
                            <th>Exchange Rate (NPR/USD)</th>
                            <th>Paid (USD)</th>
                            <th>Total Commission Payable (NPR)</th>
                            <th>Balance Due (USD)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            @php
                                // --- PHP Logic to determine display values and calculate totals ---
                                // This logic generates the initial HTML. JS will interact with it.

                                // Ensure $comissions is iterable (it might not be passed or be null)
                                $comissions = $comissions ?? collect();

                                // 1. Look up commission rules
                                $matchingCommissionRule = null;
                                $commissionTypesFromRule = []; // Default to empty array
                                $bonusFromRule = 0.0;
                                $incentiveFromRule = 0.0;

                                if ($comissions->isNotEmpty()) { // Use Eloquent collection method
                                    $matchingCommissionRule = $comissions->first(function ($rule) use ($application) {
                                        // Add null checks for rule properties just in case
                                        $ruleUniversity = $rule->university ?? '';
                                        $ruleProduct = $rule->product ?? '';
                                        $appUniversity = $application->university ?? '';
                                        $appEnglish = $application->english ?? ''; // Assuming 'english' is the product field

                                        return strtolower($ruleUniversity) == strtolower($appUniversity) &&
                                               strtolower($ruleProduct) == strtolower($appEnglish);
                                    });

                                    if ($matchingCommissionRule) {
                                        // Parse commission types from the rule
                                        if (isset($matchingCommissionRule->commission_types)) {
                                             if (is_string($matchingCommissionRule->commission_types)) {
                                                  $decodedRuleTypes = json_decode($matchingCommissionRule->commission_types, true);
                                                  if (json_last_error() === JSON_ERROR_NONE) {
                                                       $commissionTypesFromRule = $decodedRuleTypes;
                                                  } else {
                                                     // Handle JSON decode error or non-JSON string
                                                     $commissionTypesFromRule = $matchingCommissionRule->commission_types; // Keep as string if not valid JSON
                                                  }
                                             } elseif (is_array($matchingCommissionRule->commission_types)) {
                                                  $commissionTypesFromRule = $matchingCommissionRule->commission_types;
                                             } else {
                                                $commissionTypesFromRule = (string) $matchingCommissionRule->commission_types; // Cast other types to string
                                             }
                                        }

                                        // Extract bonus and incentive from rule's commission_types array/string or separate fields
                                        // Note: The migration removed bonus/intensive *fields* from commission_payable,
                                        // but the *commission_types* JSON might still contain these keys.
                                        // The logic here should extract from the JSON if available, falling back to 0 if not.
                                        // It should NOT rely on separate 'bonus'/'intensive' fields on the commission_payable model if they were removed.
                                        if (is_array($commissionTypesFromRule)) {
                                           $bonusFromRule = isset($commissionTypesFromRule['Bonus']) ? (float)$commissionTypesFromRule['Bonus'] : (isset($commissionTypesFromRule['bonus']) ? (float)$commissionTypesFromRule['bonus'] : 0.0);
                                           // Check for 'Intensive', 'intensive', and 'incentive'
                                           $incentiveFromRule = isset($commissionTypesFromRule['Intensive']) ? (float)$commissionTypesFromRule['Intensive'] : (isset($commissionTypesFromRule['intensive']) ? (float)$commissionTypesFromRule['intensive'] : (isset($commissionTypesFromRule['incentive']) ? (float)$commissionTypesFromRule['incentive'] : 0.0));
                                        } else {
                                            // If commission_types is a string or other format, we can't reliably get bonus/incentive this way.
                                            // Fallback to 0.0, or potentially try parsing the string if it's a simple structure?
                                            // For now, default to 0 if it's not a recognizable array.
                                            $bonusFromRule = 0.0;
                                            $incentiveFromRule = 0.0;
                                        }
                                    }
                                }


                                // 2. Get existing transaction data (overrides)
                                // Ensure the relationship exists on the Application model:
                                // public function commissionTransactions() { return $this->hasOne(CommissionTransaction::class); }
                                // And is eager loaded in the controller:
                                // Application::with('commissionTransactions')->get();
                                $transaction = $application->commissionTransactions;


                                // 3. Determine display values (Override > Rule > Default)
                                // Handle potential JSON string override for commission types
                                $displayedCommissionTypes = null;
                                if ($transaction && isset($transaction->commission_types_override) && $transaction->commission_types_override !== null) {
                                     if (is_string($transaction->commission_types_override)) {
                                         $decodedOverrideTypes = json_decode($transaction->commission_types_override, true);
                                          if (json_last_error() === JSON_ERROR_NONE) {
                                              $displayedCommissionTypes = $decodedOverrideTypes;
                                          } else {
                                              $displayedCommissionTypes = $transaction->commission_types_override; // Keep as string if invalid JSON
                                          }
                                     } else {
                                        $displayedCommissionTypes = $transaction->commission_types_override; // Use directly if not a string (e.g., already an array/object)
                                     }
                                } else {
                                     $displayedCommissionTypes = $commissionTypesFromRule; // Use rule if no override
                                }


                                $displayedBonus = $transaction && isset($transaction->bonus_override) && $transaction->bonus_override !== null ? (float)$transaction->bonus_override : (float)$bonusFromRule;
                                $displayedIncentive = $transaction && isset($transaction->incentive_override) && $transaction->incentive_override !== null ? (float)$transaction->incentive_override : (float)$incentiveFromRule;
                                $displayedExchangeRate = $transaction && isset($transaction->exchange_rate) && $transaction->exchange_rate !== null ? (float)$transaction->exchange_rate : 1.0;
                                $displayedPaid = $transaction && isset($transaction->paid) && $transaction->paid !== null ? (float)$transaction->paid : 0.0;


                                // 4. Format commission types for display string
                                $displayedCommissionValueString = 'N/A'; // Default
                                if (is_array($displayedCommissionTypes)) {
                                    $parts = [];
                                     foreach($displayedCommissionTypes as $key => $value) {
                                         $cleanedKey = strtolower($key);
                                         // Exclude 'bonus', 'intensive', 'incentive' from this display string
                                         if (!in_array($cleanedKey, ['bonus', 'intensive', 'incentive'])) {
                                             // Handle cases where value might be an array with 'value' key or a direct value
                                             $val = is_array($value) && isset($value['value']) ? $value['value'] : $value;

                                             // Add '%' if it looks like a numeric percentage value and doesn't already have '%'
                                             $formattedVal = $val;
                                             if (is_numeric($val) && strpos((string)$val, '%') === false) {
                                                 $formattedVal .= '%';
                                             } elseif (!is_numeric($val)) {
                                                 // If not numeric, just use the value as is (e.g., "Fixed")
                                                 $formattedVal = $val;
                                             }
                                              $parts[] = ucfirst($key) . ': ' . $formattedVal;
                                         }
                                     }
                                     $displayedCommissionValueString = implode(', ', $parts);
                                } elseif (is_string($displayedCommissionTypes) && $displayedCommissionTypes !== '') {
                                     $displayedCommissionValueString = $displayedCommissionTypes; // Use raw string if override is string
                                }


                                // 5. Calculate Total Commission in USD (based on displayed values)
                                 $calculatedCommissionAmountFromFee = 0.0;
                                 $fee = (float)($application->fee ?? 0.0);

                                  if (is_string($displayedCommissionValueString)) {
                                      // Attempt to parse percentage first from the combined string
                                      $percentageMatch = preg_match('/(\d+(\.\d+)?)\s*%/i', $displayedCommissionValueString, $matches);
                                      if ($percentageMatch && isset($matches[1])) {
                                          $percentage = (float)$matches[1];
                                           if (!is_nan($percentage)) {
                                                $calculatedCommissionAmountFromFee = $fee * ($percentage / 100);
                                           }
                                      } else {
                                           // If no percentage found, attempt to parse as a fixed USD amount from the string
                                           // Remove non-numeric characters except decimal and sign, then parse
                                           $fixedAmountString = preg_replace('/[^\d.-]/', '', $displayedCommissionValueString);
                                           $fixedAmount = (float)$fixedAmountString; // parseFloat in JS equivalent
                                           if (!is_nan($fixedAmount)) {
                                              $calculatedCommissionAmountFromFee = $fixedAmount;
                                           }
                                      }
                                  }
                                  // If displayedCommissionTypes was an array but resulted in an empty string, commissionFromFee is 0.0, which is correct.


                                // Total Commission in USD
                                $totalValueUSD = $calculatedCommissionAmountFromFee + $displayedBonus + $displayedIncentive;


                                // 6. Calculate Total Commission Payable in NPR
                                $totalCommissionPayableNPR = $totalValueUSD * $displayedExchangeRate;

                                // 7. Calculate Balance Due in USD
                                $balanceDueUSD = $totalValueUSD - $displayedPaid;


                                // --- End PHP Logic ---
                            @endphp

                            <tr>
                                {{-- Data columns matching the TH order --}}
                                <td>{{ $application->name ?? '' }}</td>
                                <td>{{ $application->university ?? '' }}</td>
                                <td>{{ $application->english ?? '' }}</td>
                                <td>{{ $application->intake ?? '' }}</td> {{-- This is now the 4th TD (index 3) --}}
                                <td class="js-fee">{{ number_format($application->fee ?? 0, 2) }}</td> {{-- Added class for JS access --}}

                                {{-- Commission Types (Editable) --}}
                                {{-- This is now the 6th TD (index 5) --}}
                                <td class="js-commission-types"> {{-- Added class for JS access --}}
                                    @if ($transaction) {{-- Only editable if a transaction record exists --}}
                                    <div class="editable-field" data-appid="{{ $application->id }}" data-field="commission_types_override">
                                        <span class="display-value">{{ $displayedCommissionValueString ?: 'N/A' }}</span>
                                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                        <div class="edit-input" style="display: none;">
                                            <input type="text" name="commission_types_override" value="{{ $displayedCommissionValueString }}" class="edit-field-input" data-original-value="{{ $displayedCommissionValueString }}">
                                        </div>
                                    </div>
                                    @else
                                        {{-- Not editable, display value only --}}
                                        <span class="display-value">{{ $displayedCommissionValueString ?: 'N/A' }}</span>
                                    @endif
                                </td>

                                {{-- Bonus (Editable) --}}
                                {{-- This is now the 7th TD (index 6) --}}
                                <td class="js-bonus"> {{-- Added class for JS access --}}
                                    @if ($transaction) {{-- Only editable if a transaction record exists --}}
                                    <div class="editable-field" data-appid="{{ $application->id }}" data-field="bonus_override">
                                        <span class="display-value">{{ number_format($displayedBonus, 2) }}</span>
                                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                        <div class="edit-input" style="display: none;">
                                            <input type="text" name="bonus_override" value="{{ $displayedBonus }}" class="edit-field-input" data-original-value="{{ $displayedBonus }}">
                                        </div>
                                    </div>
                                    @else
                                         {{-- Not editable, display value only --}}
                                         <span class="display-value">{{ number_format($displayedBonus, 2) }}</span>
                                    @endif
                                </td>

                                {{-- Incentive (Editable) --}}
                                {{-- This is now the 8th TD (index 7) --}}
                                <td class="js-incentive"> {{-- Added class for JS access --}}
                                     @if ($transaction) {{-- Only editable if a transaction record exists --}}
                                    <div class="editable-field" data-appid="{{ $application->id }}" data-field="incentive_override">
                                        <span class="display-value">{{ number_format($displayedIncentive, 2) }}</span>
                                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                        <div class="edit-input" style="display: none;">
                                            <input type="text" name="incentive_override" value="{{ $displayedIncentive }}" class="edit-field-input" data-original-value="{{ $displayedIncentive }}">
                                        </div>
                                    </div>
                                    @else
                                        {{-- Not editable, display value only --}}
                                        <span class="display-value">{{ number_format($displayedIncentive, 2) }}</span>
                                    @endif
                                </td>

                                {{-- Total (Calculated, non-editable) --}}
                                {{-- This is now the 9th TD (index 8) --}}
                                <td>$<span class="total-sum-usd" data-appid="{{ $application->id }}">{{ number_format($totalValueUSD, 2) }}</span></td>

                                {{-- Exchange Rate (Editable) --}}
                                {{-- This is now the 10th TD (index 9) --}}
                                <td class="js-exchange-rate"> {{-- Added class for JS access --}}
                                     @if ($transaction) {{-- Only editable if a transaction record exists --}}
                                    <div class="editable-field" data-appid="{{ $application->id }}" data-field="exchange_rate">
                                        <span class="display-value">{{ number_format($displayedExchangeRate, 2) }}</span>
                                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                        <div class="edit-input" style="display: none;">
                                            <input type="text" name="exchange_rate" value="{{ $displayedExchangeRate }}" class="edit-field-input" data-original-value="{{ $displayedExchangeRate }}">
                                        </div>
                                    </div>
                                    @else
                                         {{-- Not editable, display value only --}}
                                         <span class="display-value">{{ number_format($displayedExchangeRate, 2) }}</span>
                                    @endif
                                </td>

                                {{-- Paid (Editable) --}}
                                {{-- This is now the 11th TD (index 10) --}}
                                <td class="js-paid"> {{-- Added class for JS access --}}
                                     @if ($transaction) {{-- Only editable if a transaction record exists --}}
                                    <div class="editable-field" data-appid="{{ $application->id }}" data-field="paid">
                                        <span class="display-value">${{ number_format($displayedPaid, 2) }}</span>
                                        <button type="button" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                                        <div class="edit-input" style="display: none;">
                                            <input type="text" name="paid" value="{{ $displayedPaid }}" class="edit-field-input" data-original-value="{{ $displayedPaid }}">
                                        </div>
                                    </div>
                                    @else
                                        {{-- Not editable, display value only --}}
                                        <span class="display-value">${{ number_format($displayedPaid, 2) }}</span>
                                    @endif
                                </td>

                                {{-- Total Commission Payable (Calculated, non-editable) --}}
                                {{-- This is now the 12th TD (index 11) --}}
                                <td>NPR<span class="total-payable-npr" data-appid="{{ $application->id }}">{{ number_format($totalCommissionPayableNPR, 2) }}</span></td>

                                {{-- Balance Due (Calculated, non-editable) --}}
                                {{-- This is now the 13th TD (index 12) --}}
                                <td>$<span class="balance-due-usd" data-appid="{{ $application->id }}">{{ number_format($balanceDueUSD, 2) }}</span></td>

                                {{-- Action --}}
                                {{-- This is now the 14th TD (index 13) --}}
                                <td>
                                    <div class="dropdown dropstart">
                                        <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton{{ $application->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical fs-6"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $application->id }}">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.Finance.accountreceivableview', $application->id) }}">
                                                    <i class="fs-4 ti ti-file-text"></i> View Details
                                                </a>
                                            </li>
                                             {{-- Show 'Create Transaction Record' button only if no commission transaction exists --}}
                                             @if (!$transaction)
                                                <li>
                                                    {{-- Button to trigger create transaction record --}}
                                                    <button class="dropdown-item d-flex align-items-center gap-3 create-transaction-btn" type="button" data-appid="{{ $application->id }}">
                                                        <i class="fs-4 ti ti-plus"></i> Create Transaction Record
                                                    </button>
                                                </li>
                                             @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                         @if ($applications->isEmpty())
                            <tr>
                                <td colspan="14" class="text-center">No accounts receivable data available.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        @endif {{-- End the main conditional block for view switching --}}

    </div> {{-- End #financeContentArea --}}

     {{-- Import Modal for Accounts Receivable (Place outside the dynamic content area) --}}
     {{-- This modal should only be relevant when view=receivable --}}
    <div id="receivableImportModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Accounts Receivable Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Ensure this route is correct for receiving the receivable import file --}}
                    <form action="{{ route('finance.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="receivableCsvFile" class="form-label">Select File (.csv, .xlsx, .xls)</label>
                            <input type="file" class="form-control" id="receivableCsvFile" name="csvFile" accept=".csv,.xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- The Payable Import Modal is removed as the Payable table section is removed --}}


    {{-- Common styles --}}
    <style>
        /* Style for the editable fields */
        .editable-field {
            position: relative;
            display: inline-block;
            margin: 5px 0;
        }

        .display-value {
            padding: 5px;
            min-width: 80px; /* Adjust as needed */
            display: inline-block;
            word-break: break-word; /* Break long text */
        }

        .edit-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            padding: 3px 5px;
            margin-left: 5px;
            transition: color 0.2s;
            opacity: 0; /* Hide by default */
            visibility: hidden; /* Hide by default */
            transition: opacity 0.3s, visibility 0.3s;
        }

        .editable-field:hover .edit-btn {
            opacity: 1; /* Show on hover */
            visibility: visible; /* Show on hover */
        }

        .fas.fa-pencil-alt {
            font-size: 14px;
        }

        .edit-input input {
            padding: 5px 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100px; /* Adjust width as needed */
            transition: border-color 0.15s ease-in-out;
        }

        .edit-input input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

         /* Style for the switcher buttons */
         .btn-group .btn {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            color: #495057;
            border-radius: 0.25rem;
            margin-right: 0.5rem;
        }

        .btn-group .btn.active {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .btn-group .btn i {
            margin-right: 0.5rem;
        }

        .btn-group .btn:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-group .btn:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            margin-right: 0;
        }

        /* SweetAlert custom styles */
        .swal-custom-popup {
            font-family: 'Arial', sans-serif;
        }

        .swal-custom-ok-button {
            background-color: #2e7d32 !important; /* Your success color */
            border-color: #2e7d32 !important;
        }
         .swal-custom-cancel-button {
            background-color: #6c757d !important; /* Bootstrap secondary color */
            border-color: #6c757d !important;
         }

         /* Custom SweetAlert classes to remove padding and background */
        .swal-no-padding {
            padding: 0 !important;
        }

        .swal-no-background {
            background: transparent !important;
        }

        /* Ensure the loading spinner is visible */
        .swal2-show {
             /* Background for the *modal itself* should be standard unless you want a custom spinner on transparent background */
             /* If you want the *overlay* transparent black, use the overlay class */
        }

        /* Custom overlay class for transparent black background */
        .swal-transparent-black-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important; /* Semi-transparent black */
        }

        /* Style for the filter headers */
        .filter-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        /* Make the TH element containing the popup relatively positioned */
        th.filterable-header {
            position: relative;
             /* Prevent header text from wrapping too tightly when icon and popup are present */
             white-space: nowrap;
        }

        .filter-popup {
            position: absolute;
            top: 100%;
            left: 0;
            /* Adjust size and style */
            min-width: 220px; /* Ensure it's wide enough for the input */
            background-color: white;
            border: 1px solid #ccc;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: none;
            white-space: nowrap; /* Prevent content wrapping */
             margin-top: 2px; /* Optional: Add a small gap below the header */
        }

        .filter-popup input {
            width: 100%; /* Make input fill the popup width */
             box-sizing: border-box; /* Include padding/border in width */
        }

         /* Style specifically for the new filter icon if needed */
        .fas.fa-bars.filter-icon {
             /* Default Font Awesome icons are typically transparent backgrounds */
             /* Color is inherited from parent or default text color */
             /* You can explicitly set color if needed, e.g., color: #333; */
        }
    </style>

    {{-- FontAwesome for pencil icon (ensure this is included in your layout or here) --}}
    {{-- This already includes fas fa-filter and fas fa-bars --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    {{-- XLSX library for download (ensure this is included in your layout or here) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
      // Get the dynamic content area (this element is NOT replaced by AJAX)
const financeContentArea = document.getElementById('financeContentArea');
// Get the CSRF token once (this is also NOT replaced)
const csrfToken = '{{ csrf_token() }}';

// State variable for tracking inline editing (kept outside functions)
let currentlyEditingReceivable = null;

// Helper function to check if a variable is set (similar to PHP isset)
function isset(variable) {
    return typeof variable !== 'undefined' && variable !== null;
}

// Function to show the filter popup
// MODIFIED: Now handles popups for columns 0, 1, 2, and 3 (Intake)
function showFilterPopup(columnIndex, event) {
    // Hide all filter popups first, unless it's the one we are about to show/hide
    // Ensure we only target popups within the currently active table (receivable)
    // Since only receivable table exists now, we can simplify this check
    const activeTableId = 'receivableDataTable'; // Only receivable table exists

    financeContentArea.querySelectorAll(`#${activeTableId} .filter-popup`).forEach(popup => {
        if (popup.id !== `filterPopup${columnIndex}`) {
            popup.style.display = 'none';
        }
    });

    const popup = document.getElementById(`filterPopup${columnIndex}`);

    // Toggle display: If clicking the same icon again, hide it. Otherwise, show it.
    if (popup && popup.style.display === 'block') {
        popup.style.display = 'none';
    } else if (popup) {
        // The positioning is now handled by CSS (top: 100%, left: 0 relative to the TH)
        // We just need to make it visible.
        popup.style.display = 'block';

        // Focus on the input field after showing
        const input = popup.querySelector('input');
        if (input) {
            input.focus();
        }
    }

    // Prevent the click from closing other popups immediately via the document listener
    event.stopPropagation();
}


// Function to filter table based on column index and filter value
// This function remains the same, it filters the table body rows based on the index passed.
// It's currently only used for the Receivable table's column filters.
function filterTable(columnIndex, filterValue) {
    const table = document.getElementById("receivableDataTable"); // Explicitly target receivable table
    if (!table) return;

    const rows = table.getElementsByTagName("tr");
    const filter = filterValue.toLowerCase();

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        // Skip rows that are for "No data" messages etc. if any
        if (rows[i].classList.contains('no-data-row')) continue;

        const cells = rows[i].getElementsByTagName("td");
        if (cells.length > columnIndex) {
            const cellText = cells[columnIndex].textContent.toLowerCase();
            if (cellText.includes(filter)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

// ADDED BACK: document click listener for hiding popups when clicking outside
document.addEventListener('click', function(event) {
    // Check if the click is outside any filter icon AND outside any filter popup
    // Ensure we only hide popups related to the current view's table
    // Since only receivable table exists now, we can simplify this check
    const activeTableId = 'receivableDataTable'; // Only receivable table exists


    const isClickInsideFilterIcon = event.target.closest(`#${activeTableId} .filter-icon`);
    const isClickInsideFilterPopup = event.target.closest(`#${activeTableId} .filter-popup`);

    if (!isClickInsideFilterIcon && !isClickInsideFilterPopup) {
        financeContentArea.querySelectorAll(`#${activeTableId} .filter-popup`).forEach(popup => {
            popup.style.display = 'none';
        });
    }
});


// --- View-specific Functions (Global scope so they can be called from event listeners) ---

// --- Receivable Specific Functions ---
function filterReceivableTable() {
    // This function is used for the main search input, not the column filters
    const input = document.getElementById("receivableSearchInput");
    const table = document.getElementById("receivableDataTable");
    if (!input || !table) return;

    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        // Skip rows that are for "No data" messages etc. if any
        if (rows[i].classList.contains('no-data-row')) continue;

        const cells = rows[i].getElementsByTagName("td");
        let rowMatches = false;
        // Search across relevant columns (these indices match the TD columns again)
        // Indices should match the order in the tbody (Name, University, Product, Intake, ...)
        if (cells.length > 0 && cells[0] && cells[0].textContent.toLowerCase().includes(filter)) rowMatches = true; // Name (index 0)
        if (!rowMatches && cells.length > 1 && cells[1] && cells[1].textContent.toLowerCase().includes(filter)) rowMatches = true; // University (index 1)
        if (!rowMatches && cells.length > 2 && cells[2] && cells[2].textContent.toLowerCase().includes(filter)) rowMatches = true; // Product (index 2)
        if (!rowMatches && cells.length > 3 && cells[3] && cells[3].textContent.toLowerCase().includes(filter)) rowMatches = true; // Intake (index 3)
        // Add other columns if needed for the main search

        rows[i].style.display = rowMatches ? "" : "none";
    }
}

function downloadReceivableData() {
    const table = document.getElementById("receivableDataTable");
    if (!table) {
        Swal.fire('Error', 'Accounts Receivable table not found to download.', 'error');
        return;
    }
    const tableClone = table.cloneNode(true);
    // Remove filter popups from the clone
    tableClone.querySelectorAll('.filter-popup').forEach(el => el.remove());
    // Remove edit buttons and dropdowns from the clone
    tableClone.querySelectorAll('.edit-btn, .dropdown').forEach(el => el.remove());
    // Remove filter icons from the clone
     tableClone.querySelectorAll('.filter-icon').forEach(el => el.remove());
     // Remove empty "No data" rows if present
    tableClone.querySelectorAll('.no-data-row').forEach(el => el.remove());


    const wb = XLSX.utils.table_to_book(tableClone, { sheet: "Accounts Receivable Data" });
    XLSX.writeFile(wb, "accounts_receivable_data.xlsx");
}

// The openReceivableImportModal function relies on Bootstrap's data attributes for the modal button.
// The button should have data-bs-toggle="modal" and data-bs-target="#receivableImportModal".
// We don't need a separate JS function to open it if the button has these attributes.
// The handler for the button itself is removed, relying on Bootstrap's built-in behavior.


// --- Payable Specific Functions (Removed as Payable section is removed) ---
/*
function filterPayableTable() {
    // ... removed ...
}

function downloadPayableData() {
   // ... removed ...
}
*/


// --- View Switching Logic (AJAX) ---
async function loadFinanceView(viewType) {
    // This route is kept as per your original code structure
    // Ensure your backend route `/finance` handles the `view` query parameter
    const url = `{{ route('backend.Finance.index') }}?view=${viewType}`;

    const loadingSwal = Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        showConfirmButton: false,
        customClass: {
            popup: 'swal-no-padding',
            container: 'swal-no-background', // Maybe remove this one if you want a standard modal look
            overlay: 'swal-transparent-black-overlay' // Custom overlay class
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            const errorText = await response.text();
            // Try to parse JSON error first
             try {
                 const errorData = JSON.parse(errorText);
                 throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
             } catch (e) {
                 // If not JSON, use raw text (maybe truncated)
                  throw new Error(`HTTP error! status: ${response.status}. Response: ${errorText.substring(0, 200)}...`);
             }
        }

        const html = await response.text();
        // Parse the HTML to find the inner content of #financeContentArea
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const fetchedContentArea = doc.getElementById('financeContentArea');

        if (fetchedContentArea) {
            // Replace the inner HTML of the existing financeContentArea
            financeContentArea.innerHTML = fetchedContentArea.innerHTML;
            // Re-attach event listeners *after* new content is loaded into the DOM
            // Since only 'receivable' view content is now ever loaded into financeContentArea,
            // we can always set up receivable listeners, or let setupViewListeners handle the 'viewType'.
            // Keeping viewType helps if the backend *does* return different content later.
            setupViewListeners(viewType); // Pass the requested viewType
            currentlyEditingReceivable = null; // Reset editing state when view changes
        } else {
            console.error("Could not find #financeContentArea in the fetched HTML.");
            Swal.fire('Error', 'Could not load section content structure.', 'error');
        }

    } catch (error) {
        console.error('Error loading finance view:', error);
        Swal.fire('Error', 'Failed to load content: ' + error.message, 'error');
    } finally {
        loadingSwal.close();
    }
}

// Sets up event listeners for the currently active view
// Called after the content of financeContentArea is replaced by AJAX
function setupViewListeners(viewType) {
     // Clear previous specific listeners *before* adding new ones
     // (Search inputs and download buttons might have previous listeners)
     const prevReceivableSearchInput = document.getElementById('receivableSearchInput');
     if (prevReceivableSearchInput) prevReceivableSearchInput.removeEventListener('keyup', filterReceivableTable);
     const prevReceivableDownloadButton = document.getElementById('receivableDownloadButton');
     if (prevReceivableDownloadButton) prevReceivableDownloadButton.removeEventListener('click', downloadReceivableData);

     // The Payable listeners are removed as the section is removed.
     // const prevPayableSearchInput = document.getElementById('payableSearchInput');
     // if (prevPayableSearchInput) prevPayableSearchInput.removeEventListener('keyup', filterPayableTable);
     // const prevPayableDownloadButton = document.getElementById('payableDownloadButton');
     // if (prevPayableDownloadButton) prevPayableDownloadButton.removeEventListener('click', downloadPayableData);

     // Attach listeners based on the *new* content's elements
    // Since only receivable content is loaded, we always attach receivable listeners if elements exist
    const receivableSearchInput = document.getElementById('receivableSearchInput');
    if (receivableSearchInput) {
        receivableSearchInput.addEventListener('keyup', filterReceivableTable);
    }
    const receivableDownloadButton = document.getElementById('receivableDownloadButton');
    if (receivableDownloadButton) {
        receivableDownloadButton.addEventListener('click', downloadReceivableData);
    }
     // Delegated listeners for edit/create buttons are handled by the main document/financeContentArea listeners
     // Column filter listeners are handled by the main document listener for filter popups

     // No specific actions needed for payable table actions as it's removed
}

// Handler for switcher button clicks (delegated on financeContentArea)
function handleSwitchButtonClick(e) {
     const button = e.target.closest('.btn-group .btn');
     // Ensure click is on a switcher button *within* the financeContentArea
     if (!button || !financeContentArea.contains(button)) return;

     const viewType = button.dataset.view;
     // Update button active state instantly for better UX
     button.closest('.btn-group').querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
     button.classList.add('active');

     // Even if clicking "Payable", the AJAX call goes through, and the backend
     // will determine what content to return (presumably the Receivable view again
     // or handle the 'payable' route differently if it's still defined).
     loadFinanceView(viewType);
}

// Handler for create transaction button clicks (delegated on financeContentArea)
function handleCreateTransactionClick(e) {
    const createBtn = e.target.closest('.create-transaction-btn');
    // Ensure click is on a create button *within* the receivable DataTable
    if(createBtn && createBtn.closest('#receivableDataTable')) {
        e.preventDefault(); // Prevent default link/button action
        const appId = createBtn.dataset.appid;
        Swal.fire({
            title: 'Create Transaction Record?',
            text: `Do you want to create a new transaction record for this application? This enables editing commission details.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create it!',
            cancelButtonText: 'No, cancel',
            customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button', cancelButton: 'swal-custom-cancel-button' }
        }).then((result) => {
            if (result.isConfirmed) {
                createCommissionTransaction(appId);
            }
        });
    }
}

// Function to save data via AJAX
async function saveReceivableData(appId, field, value) {
    // IMPORTANT: REPLACE 'YOUR_RECEIVABLE_UPDATE_ROUTE' with the actual Laravel route URL
    // that handles updating commission transaction records via POST/PUT.
    // Example: route('backend.finance.update_transaction', ['application' => $appId])
    // You'll likely pass the application ID as a parameter in the route URL or in the form data.
    // The backend endpoint needs to find the related CommissionTransaction record (or create one if needed, though the button handles initial creation)
    // and update the specific field.
    const updateUrl = `{{ url('/backend/finance/transaction/update') }}/${appId}`; // Example route - adjust as needed! Assuming '/backend/finance/transaction/update/{application}'

     // Add a check for a placeholder URL before proceeding
     if (updateUrl.includes('/YOUR_RECEIVABLE_UPDATE_ROUTE')) {
         console.error("Update route for receivable not set!");
         Swal.fire('Configuration Error', 'The route for saving receivable data is not configured.', 'error');
         throw new Error("Update route not configured.");
     }


    const formData = new FormData();
    formData.append('field', field); // e.g., 'bonus_override', 'exchange_rate'
    formData.append('value', value);
    formData.append('_token', csrfToken); // Use the pre-defined token
    formData.append('_method', 'PUT'); // Or 'PATCH', depending on your route definition


    const loadingSwal = Swal.fire({
        title: 'Saving...',
        html: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false, // Hide default OK button
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const response = await fetch(updateUrl, {
            method: 'POST', // Use POST if spoofing PUT/PATCH, otherwise use PUT/PATCH directly
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Check for network errors or non-2xx status
        if (!response.ok) {
             const errorText = await response.text();
             console.error("Server response error:", errorText);
             // Try to parse JSON error (e.g., validation errors)
             const contentType = response.headers.get("content-type");
             if (contentType && contentType.includes("application/json")) {
                 const errorData = await response.json();
                 if (response.status === 422 && errorData.errors) {
                     // Handle specific validation errors
                     const errorMessages = Object.values(errorData.errors).flat().join('<br>');
                     throw new Error('Validation Error: ' + errorMessages);
                 } else {
                     // Handle other JSON errors
                      throw new Error(errorData.message || `Server error: ${response.status}`);
                 }
             } else {
                 // Handle non-JSON errors (e.g., Laravel error page)
                 throw new Error(`HTTP error! status: ${response.status}. Response content type: ${contentType}. Response body: ${errorText.substring(0, 200)}...`);
             }
        }

         // If response is OK (2xx), parse the JSON
         const data = await response.json();

        loadingSwal.close(); // Close loading modal on success

        // Show success message
        Swal.fire({
            title: 'Saved!',
            text: data.message || "Changes saved successfully.",
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' }
        });

        return data; // Return the successful data

    } catch (error) {
         loadingSwal.close(); // Ensure loading modal is closed on error
         console.error('Error saving data:', error);
         // Show error message to the user
         Swal.fire('Error', "An error occurred while saving: " + error.message, 'error');
         throw error; // Re-throw the error so calling code can handle it (e.g., revert display)
    }
}

async function createCommissionTransaction(appId) {
     // IMPORTANT: REPLACE 'YOUR_CREATE_TRANSACTION_ROUTE' with the actual Laravel route URL
     // that handles creating a commission transaction record for an application.
     // Example: route('backend.finance.store_transaction') or route('backend.applications.transactions.store', $appId)
     // Your original code had {{ route('stores.index') }} which is likely incorrect.
    const createUrl = '{{ url('/backend/finance/transaction/store') }}'; // Example route - adjust as needed!

     // Add a check for a placeholder URL before proceeding
     if (createUrl.includes('/stores/')) { // Check if it looks like the old incorrect route
         console.error("Create transaction route not set!");
         Swal.fire('Configuration Error', 'The route for creating a transaction record is not configured.', 'error');
         throw new Error("Create route not configured.");
     }


    const formData = new FormData();
    formData.append('application_id', appId);
    formData.append('_token', csrfToken);

    const loadingSwal = Swal.fire({
        title: 'Creating Record',
        html: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false, // Hide default OK button
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const response = await fetch(createUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Check for network errors or non-2xx status
        if (!response.ok) {
             const errorText = await response.text();
             console.error("Server response error:", errorText);
            const contentType = response.headers.get("content-type");
             if (contentType && contentType.includes("application/json")) {
                 const errorData = await response.json();
                  throw new Error(errorData.message || `Server error: ${response.status}`);
             } else {
                 throw new Error(`HTTP error! status: ${response.status}. Response content type: ${contentType}. Response body: ${errorText.substring(0, 200)}...`);
             }
        }

        const data = await response.json();

        loadingSwal.close(); // Close loading modal

        Swal.fire({
            title: 'Success!',
            text: data.message || "Transaction record created successfully.",
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-custom-ok-button' }
        }).then(() => {
            // Reload the current view to show the updated state (e.g., editable fields appear)
            // Since only receivable view exists in this Blade, we always load receivable.
            loadFinanceView('receivable');
        });

        return data; // Return successful data
    } catch (error) {
         loadingSwal.close(); // Ensure loading modal is closed on error
         console.error('Error creating transaction:', error);
         Swal.fire('Error', 'Failed to create record: ' + error.message, 'error');
         throw error; // Re-throw
    }
}

// Function to update calculated fields in a receivable row
function updateReceivableCalculations(appId) {
    const table = document.getElementById('receivableDataTable');
    if (!table) return; // Table not found in current view

    // Find the specific row for the application ID
    const row = table.querySelector(`tr .editable-field[data-appid="${appId}"], tr .total-sum-usd[data-appid="${appId}"]`)?.closest('tr');

    if (!row) {
        console.warn(`Row not found for appId ${appId} for calculation update.`);
        return;
    }

    // Get values from the appropriate source (input field if editing, display span otherwise)
    // Use optional chaining (?.) for safety
    const getFieldValue = (selector, isNumeric = false) => {
        const editableDiv = row.querySelector(selector);
        if (!editableDiv) {
             // Fallback to cell content if not editable or editable div missing structure
             const cell = row.querySelector(selector.split('.editable-field')[0]); // Get the TD selector part
             const cellText = cell?.textContent?.trim() || '';
             if (isNumeric) {
                 // Attempt to parse numeric value from cell text
                 const numericValue = parseFloat(cellText.replace(/[^0-9.-]+/g,""));
                 return isNaN(numericValue) ? 0 : numericValue;
             }
             return cellText;
        }
        const inputField = editableDiv.querySelector('.edit-field-input');
        const displayValueSpan = editableDiv.querySelector('.display-value');

        let value = '';
        if (inputField && inputField.closest('.edit-input').style.display !== 'none') {
             // Get value from input if it's visible (being edited)
             value = inputField.value.trim();
        } else if (displayValueSpan) {
             // Get value from display span if input is hidden
             value = displayValueSpan.textContent.trim();
        }

        if (isNumeric) {
             // Attempt to parse numeric value, removing symbols like '$', ',', 'NPR'
             const numericValue = parseFloat(value.replace('$', '').replace(/,/g, '').replace('NPR', '').trim());
             return isNaN(numericValue) ? 0 : numericValue;
        }

        return value;
    };

    // Get the required values
    // Fee is not editable, get directly from cell (using class added in Blade)
    const fee = parseFloat(row.querySelector('.js-fee')?.textContent?.replace(/[^0-9.-]+/g,"")) || 0;
    const commissionTypeText = getFieldValue('.js-commission-types .editable-field'); // Get string like "Type: 10%, Type2: 5%" or "200"
    const bonus = getFieldValue('.js-bonus .editable-field', true); // Pass true for numeric parsing
    const incentive = getFieldValue('.js-incentive .editable-field', true); // Pass true for numeric parsing
    const exchangeRate = getFieldValue('.js-exchange-rate .editable-field', true) || 1.0; // Default rate to 1 if parse fails
    const paid = getFieldValue('.js-paid .editable-field', true); // Pass true for numeric parsing


    let calculatedCommissionAmountFromFee = 0;
    if (commissionTypeText && commissionTypeText !== 'N/A') {
        try {
            // Attempt to parse percentage from the string (like "Type: 10%", or just "10%")
            const percentageMatch = commissionTypeText.match(/(\d+(\.\d+)?)\s*%/i); // Case-insensitive '%'
             if (percentageMatch && isset(percentageMatch[1])) {
                const percentage = parseFloat(percentageMatch[1]);
                if (!isNaN(percentage)) {
                    calculatedCommissionAmountFromFee = fee * (percentage / 100);
                }
            } else {
                // If no percentage, attempt to parse as a fixed USD amount from the string
                // Remove any non-numeric characters (except decimal and sign)
                const fixedAmountString = commissionTypeText.replace(/[^0-9.-]/g, '');
                const fixedAmount = parseFloat(fixedAmountString);
                 if (!isNaN(fixedAmount)) {
                    calculatedCommissionAmountFromFee = fixedAmount;
                 }
            }
        } catch (e) {
            console.error("Error parsing commission text for calculation:", commissionTypeText, e);
            calculatedCommissionAmountFromFee = 0;
        }
    }

    // Calculate totals
    const totalValueUSD = calculatedCommissionAmountFromFee + bonus + incentive;
    const totalCommissionPayableNPR = totalValueUSD * exchangeRate;
    const balanceDueUSD = totalValueUSD - paid;

    // Update display elements (using the specific span elements)
     const totalSumElem = row.querySelector('.total-sum-usd');
     const totalPayableElem = row.querySelector('.total-payable-npr');
     const balanceDueElem = row.querySelector('.balance-due-usd');

    if (totalSumElem) totalSumElem.textContent = totalValueUSD.toFixed(2);
    if (totalPayableElem) totalPayableElem.textContent = totalCommissionPayableNPR.toFixed(2);
    if (balanceDueElem) balanceDueElem.textContent = balanceDueUSD.toFixed(2);
}


// Completes the inline editing process (triggered on blur or Enter/Escape)
function handleReceivableEditCompletion(container, save = true) {
    const inputField = container.querySelector('.edit-field-input');
    const displayValueSpan = container.querySelector('.display-value');
    const editInputDiv = container.querySelector('.edit-input');

    const appId = container.dataset.appid;
    const field = container.dataset.field;
    const newValue = inputField.value.trim();
    const originalValue = inputField.dataset.originalValue.trim(); // Use stored original value

    // Hide input and show display value immediately
    displayValueSpan.style.display = 'inline-block';
    editInputDiv.style.display = 'none';
    currentlyEditingReceivable = null; // Reset editing state

    // Only proceed if save is true AND value has changed
    if (save && newValue !== originalValue) {
        Swal.fire({
            title: 'Save Changes?',
            text: `Do you want to save "${newValue}" for ${field.replace('_override', '').replace('_', ' ')}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'No, cancel',
            customClass: {
                popup: 'swal-custom-popup',
                confirmButton: 'swal-custom-ok-button',
                cancelButton: 'swal-custom-cancel-button'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                 // --- Save logic ---
                saveReceivableData(appId, field, newValue)
                    .then(() => {
                        // Update original value marker only on successful save
                        inputField.dataset.originalValue = newValue;

                        // Format display value based on field type after successful save
                         let formattedNewValueForDisplay = newValue;
                         if (field === 'bonus_override' || field === 'incentive_override' || field === 'exchange_rate') {
                             const numericValue = parseFloat(newValue) || 0;
                             formattedNewValueForDisplay = numericValue.toFixed(2);
                         } else if (field === 'paid') {
                              const numericValue = parseFloat(newValue) || 0;
                             formattedNewValueForDisplay = '$' + numericValue.toFixed(2);
                         } else if (field === 'commission_types_override') {
                             formattedNewValueForDisplay = newValue; // Keep complex strings as is
                         }
                         displayValueSpan.textContent = formattedNewValueForDisplay; // Update the visible text

                        updateReceivableCalculations(appId); // Recalculate totals
                    })
                    .catch(() => {
                        // --- Handle Save Error ---
                        // Error message is shown in saveReceivableData catch block.
                        // Restore original value in input field and display span.
                        restoreReceivableOriginalValue(container);
                        updateReceivableCalculations(appId); // Recalculate using original values
                    });
            } else {
                // --- Handle User Cancelling Save ---
                 restoreReceivableOriginalValue(container);
                 updateReceivableCalculations(appId); // Recalculate using original values
            }
        });
    } else {
        // If save is false (Escape key) or value hasn't changed
        restoreReceivableOriginalValue(container); // Ensure display value matches the original value marker
        // No need to recalculate if value didn't change or user cancelled without change
    }
}

// Helper function to restore the original value in the input and display span
function restoreReceivableOriginalValue(container) {
     const inputField = container.querySelector('.edit-field-input');
     const displayValueSpan = container.querySelector('.display-value');
     const originalValue = inputField.dataset.originalValue.trim(); // Use stored original value
     const field = container.dataset.field;

     inputField.value = originalValue; // Restore input value

     // Restore original display value in span (with formatting)
     let formattedOriginalValue = originalValue;
     if (field === 'bonus_override' || field === 'incentive_override' || field === 'exchange_rate') {
         const numericOriginalValue = parseFloat(originalValue) || 0;
         formattedOriginalValue = numericOriginalValue.toFixed(2);
     } else if (field === 'paid') {
          const numericOriginalValue = parseFloat(originalValue) || 0;
          formattedOriginalValue = '$' + numericOriginalValue.toFixed(2);
     } else if (field === 'commission_types_override') {
         formattedOriginalValue = originalValue; // Keep complex strings as is
     } else {
          // Handle cases where original value might have been "N/A" or similar
          formattedOriginalValue = originalValue || 'N/A';
     }
     displayValueSpan.textContent = formattedOriginalValue;
}


// --- Initial Setup and Global Listeners ---
document.addEventListener('DOMContentLoaded', function() {

    // Delegate click listener for switcher buttons to the financeContentArea
    // Use a listener on the static financeContentArea for elements *within* it
    financeContentArea.addEventListener('click', handleSwitchButtonClick);

    // Event delegation for inline editing within #receivableDataTable
    // Listen on financeContentArea as it's static, but check if the event target is *inside* #receivableDataTable
    financeContentArea.addEventListener('click', function(e) {

         // Check if the click is inside the receivable DataTable
         const receivableTable = e.target.closest('#receivableDataTable');
         if (!receivableTable) return; // Ignore clicks outside receivable table

         // Check if the target is an edit button *within* the receivable DataTable
        const editButton = e.target.closest('.edit-btn');

        if (editButton) {
            e.stopPropagation(); // Stop event propagation to prevent interfering with other listeners (like hiding filter popups)
            const container = editButton.closest('.editable-field');

            // If another field is already being edited, complete that edit first (saves or cancels)
            if (currentlyEditingReceivable && currentlyEditingReceivable !== container) {
                 // Blur the input field of the currently edited element to trigger its blur handler
                 const currentInput = currentlyEditingReceivable.querySelector('.edit-field-input');
                 if(currentInput && document.activeElement === currentInput) {
                    currentInput.blur(); // This will trigger the blur handler below
                 } else {
                     // If the focus isn't where we expect, force completion without saving (as blur didn't happen)
                      handleReceivableEditCompletion(currentlyEditingReceivable, false); // Pass false to indicate cancel/no save
                 }

                 // Then proceed to edit the new field. Use a small timeout to avoid
                 // conflicts if the blur handler also modifies DOM or state.
                 setTimeout(() => {
                     // Check if currentlyEditingReceivable is null after the blur handler runs
                    if (!currentlyEditingReceivable) {
                         startEditingReceivable(container);
                    } else {
                         // If for some reason the previous edit didn't complete, maybe show a warning
                         console.warn("Previous edit didn't complete, cannot start new edit.");
                         // Optional: Swal.fire('Warning', 'Please finish editing the current field first.', 'warning');
                    }
                 }, 50); // Adjust delay if needed
            } else if (!currentlyEditingReceivable) {
                 // Start editing if not already editing any field
                 startEditingReceivable(container);
            }
        }
         // Clicks outside the editable field but inside the table could potentially
         // close an active edit field. The blur handler on the input covers this
         // for mouse clicks that shift focus.

    });

     // Delegate blur event listener for inline editing input fields
     // Listen on financeContentArea and check if the blur happened on a specific input
     financeContentArea.addEventListener('focusout', function(e) {
        const blurredElement = e.target;
        // Check if the element losing focus is an input inside an editable field within the receivable table
        if (blurredElement.classList.contains('edit-field-input') && blurredElement.closest('.editable-field') && blurredElement.closest('#receivableDataTable')) {
             const container = blurredElement.closest('.editable-field');
             // Use a small timeout to allow clicks on other parts of the page (like save buttons in a more complex UI) to register before blur processing
             setTimeout(() => {
                 // Check if the focus has moved *outside* the container
                 // relatedTarget is the element receiving focus
                 if (!container.contains(e.relatedTarget)) {
                     if (currentlyEditingReceivable === container) {
                          handleReceivableEditCompletion(container); // Complete the edit (will prompt save)
                     } else {
                         // This shouldn't happen often if `currentlyEditingReceivable` state is managed correctly,
                         // but as a safeguard, hide if an input inside a container loses focus unexpectedly.
                         const displayValueSpan = container.querySelector('.display-value');
                         const editInputDiv = container.querySelector('.edit-input');
                         if (displayValueSpan && editInputDiv) {
                              displayValueSpan.style.display = 'inline-block';
                              editInputDiv.style.display = 'none';
                         }
                     }
                 }
             }, 50); // Adjust delay if needed
        }
     });


    // Helper function to start the editing process
    function startEditingReceivable(container) {
        currentlyEditingReceivable = container;
        const displayValue = container.querySelector('.display-value');
        const editInput = container.querySelector('.edit-input');
        const inputField = editInput.querySelector('input');

        // Store the original value when editing starts
        // Ensure we store the *cleaned* value if needed for comparison, but the raw value from input is usually fine
        inputField.dataset.originalValue = inputField.value.trim();

        displayValue.style.display = 'none';
        editInput.style.display = 'block';
        inputField.focus();
        inputField.select(); // Select text for easy editing
    }


    // Handle Enter key for inline editing only within the active input inside #financeContentArea
    financeContentArea.addEventListener('keydown', function(e) {
        const activeInput = document.activeElement;
        // Check if an input field within a currently edited receivable field is active
        if (currentlyEditingReceivable && e.key === 'Enter' && activeInput && currentlyEditingReceivable.contains(activeInput) && activeInput.classList.contains('edit-field-input')) {
            e.preventDefault(); // Prevent form submission or new line
            handleReceivableEditCompletion(currentlyEditingReceivable); // Complete the edit (will prompt save)
        }
    });

     // Handle Escape key to cancel editing
     financeContentArea.addEventListener('keydown', function(e) {
        const activeInput = document.activeElement;
        if (currentlyEditingReceivable && e.key === 'Escape' && activeInput && currentlyEditingReceivable.contains(activeInput) && activeInput.classList.contains('edit-field-input')) {
            e.preventDefault(); // Prevent default action
            // Restore original value and hide input without saving
            restoreReceivableOriginalValue(currentlyEditingReceivable);

            const displayValueSpan = currentlyEditingReceivable.querySelector('.display-value');
            const editInputDiv = currentlyEditingReceivable.querySelector('.edit-input');
             // Hide input and show display value
            if (displayValueSpan && editInputDiv) {
                displayValueSpan.style.display = 'inline-block';
                editInputDiv.style.display = 'none';
            }
            currentlyEditingReceivable = null; // Reset editing state

            // Recalculating is not strictly necessary on escape if restoreOriginalValue updates the span,
            // but if you want to be sure, you can call it.
            // updateReceivableCalculations(currentlyEditingReceivable.dataset.appid);
        }
     });

     // Delegate click listener for the 'Create Transaction Record' button
     // Listen on financeContentArea and check if the click is on the specific button within the table
     financeContentArea.addEventListener('click', handleCreateTransactionClick);


    // Initial setup based on server-loaded view
    // This runs once on page load to set up listeners for the initial content
    const initialViewButton = document.querySelector('.btn-group .btn.active');
    if (initialViewButton) {
        // Pass the initial active view type to setup listeners
        setupViewListeners(initialViewButton.dataset.view);
    } else {
        console.warn("No active view button found on initial load. Defaulting to receivable listeners.");
         // Default to setting up receivable listeners if no active button found (fallback)
         setupViewListeners('receivable');
    }
});
    </script>

@endsection