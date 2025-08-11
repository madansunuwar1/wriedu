@extends('layouts.admin')
@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Added libraries for PDF generation --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
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

<div class="main-container">
    <div class="widget-content searchable-container list">

        <!-- Header -->
        <div class="card card-body mb-4 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title">Accounts Payable</h3>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <button type="button" class="btn btn-success" id="downloadPDFBtn">
                        <i class="fas fa-file-pdf me-2"></i>Download as PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Invoice Container -->
        <div class="invoice-container" id="invoice-container">
            @foreach ($applications as $application)
            @php
            // Initialize variables
            $commissionValue = '';
            $matchingCommission = null;
            $commissionRate = 0;
            $bonusValue = 0;
            $incentiveValue = 0;
            $progressiveCommissionValue = 0;
            $paidValue = 0;
            $exchangeRate = 1;
            $calculatedCommissionAmount = 0;

            if (!isset($application->university) || !isset($application->english)) continue;

            foreach ($comission_payable as $commission) {
            if (
            isset($commission->university) && !empty($commission->university) &&
            strtolower(trim($commission->university)) == strtolower(trim($application->university)) &&
            strtolower(trim($commission->product)) == strtolower(trim($application->english))
            ) {
            $matchingCommission = $commission;
            break;
            }
            }

            if ($matchingCommission) {
            $commissionTypes = is_string($commission->commission_types) ? json_decode($commission->commission_types, true) : $commission->commission_types;
            if (is_array($commissionTypes)) {
            foreach ($commissionTypes as $key => $value) {
            $lowerKey = strtolower($key);
            if (!in_array($lowerKey, ['bonus', 'intensive', 'incentive'])) {
            $commissionRate = (float)$value / 100;
            $commissionValue = $key . ': ' . $value . '%';
            }
            }
            }
            $bonusValue = $matchingCommission->has_bonus_commission ? (float)$matchingCommission->bonus_commission : 0;
            $incentiveValue = $matchingCommission->has_incentive_commission ? (float)$matchingCommission->incentive_commission : 0;
            $calculatedCommissionAmount = (float)$application->fee * $commissionRate;
            }

            $progressiveCommissionValue = $matchingCommission && $matchingCommission->has_progressive_commission ? (float)$matchingCommission->progressive_commission : 0;

            // Commission transaction logic
            $commissionTransaction = optional($application)->commission_transaction;
            $paidValue = is_numeric(optional($commissionTransaction)->paid) ? (float)$commissionTransaction->paid : 0;
            $exchangeRate = is_numeric(optional($commissionTransaction)->exchange_rate) ? (float)$commissionTransaction->exchange_rate : 1;

            $totalValue = $calculatedCommissionAmount + $bonusValue + $incentiveValue + $progressiveCommissionValue;
            $totalValueNPR = $totalValue * $exchangeRate;
            $balanceDueNPR = max(0, $totalValueNPR - $paidValue);
            @endphp

            <!-- Invoice Document -->
            <div class="invoice-document" data-appid="{{ $application->id }}"
                data-fee="{{ $application->fee }}"
                data-bonus="{{ $bonusValue }}"
                data-incentive="{{ $incentiveValue }}"
                data-progressive="{{ $progressiveCommissionValue }}"
                data-commission-transaction-id="{{ optional($commissionTransaction)->id }}">

                <!-- Header with curved background -->
                <div class="header-bg">
                    <div class="header-content">
                        <div class="logo-section">
                            <div class="company-name">WRI Education Consultancy</div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="d-flex justify-content-between w-100">
                        <div class="invoice-title">INVOICE</div>
                        <div class="logo-icon">
                            {{-- BEST PRACTICE: Use asset() helper for paths --}}
                            <img src="{{ asset('img/wri.png') }}" width="80" alt="Company Logo" crossorigin="anonymous">
                        </div>
                    </div>
                    <div class="invoice-details">
                        <div class="invoice-info">
                            <strong>Invoice Number:</strong> INV-{{ $application->id }}<br>
                            <strong>Invoice Date:</strong> {{ now()->format('M d, Y') }}<br>
                            <strong>Due Date:</strong> {{ now()->addDays(5)->format('M d, Y') }}
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="billing-section">
                    <div class="bill-to">
                        <div class="section-title">RECEIVE FROM:</div>
                        <div class="billing-details">
                            <strong>Name:</strong> {{ $application->name }}<br>
                            <strong>Product:</strong> {{ $application->english }}<br>
                            <strong>University:</strong> {{ $application->university ?? 'N/A' }}<br>
                            <strong>Intake:</strong> {{ $application->intake ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="payment-info">
                        <div class="section-title">PAYMENT INSTRUCTIONS:</div>
                        <div class="payment-details">
                            Pay Cheque to:<br>
                            <strong>{{ $application->name }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-container">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>ITEM</th>
                                <th>DESCRIPTION</th>
                                <th>COMMISSION</th>
                                <th>RATE</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Tuition Fees Paid</td>
                                <td>{{ $commissionValue }}</td>
                                <td>${{ number_format($application->fee, 2) }}</td>
                                <td>${{ number_format($calculatedCommissionAmount, 2) }}</td>
                            </tr>
                            @if($bonusValue > 0)
                            <tr>
                                <td>2</td>
                                <td>Bonus</td>
                                <td></td>
                                <td>${{ number_format($bonusValue, 2) }}</td>
                                <td>${{ number_format($bonusValue, 2) }}</td>
                            </tr>
                            @endif
                            @if($incentiveValue > 0)
                            <tr>
                                <td>{{ $bonusValue > 0 ? '3' : '2' }}</td>
                                <td>Incentive</td>
                                <td></td>
                                <td>${{ number_format($incentiveValue, 2) }}</td>
                                <td>${{ number_format($incentiveValue, 2) }}</td>
                            </tr>
                            @endif
                            @if($progressiveCommissionValue > 0)
                            <tr>
                                <td>{{ ($bonusValue > 0 ? 1 : 0) + ($incentiveValue > 0 ? 1 : 0) + 2 }}</td>
                                <td>Progressive Commission</td>
                                <td></td>
                                <td>${{ number_format($progressiveCommissionValue, 2) }}</td>
                                <td>${{ number_format($progressiveCommissionValue, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="totals-section">
                    <div class="totals-box">
                        <div class="total-row subtotal">
                            <span>Subtotal</span>
                            <span>${{ number_format($totalValue, 2) }}</span>
                        </div>
                        <div class="total-row exchange-rate editable-field"
                            data-appid="{{ $application->id }}" data-field="exchange_rate"
                            data-commission-transaction-id="{{ optional($commissionTransaction)->id }}">
                            <span>Exchange Rate</span>
                            <div class="exchange-rate-controls">
                                <span class="display-value">NPR {{ number_format($exchangeRate, 2) }}</span>
                                <div class="edit-input" style="display: none;">
                                    <input type="number" name="exchange_rate" value="{{ $exchangeRate }}" step="0.0001" min="0.0001" class="edit-field-input">
                                </div>
                            </div>
                        </div>
                     <div class="total-row total-npr">
    <span>Total</span>
    {{-- This new wrapper is the key to the fix --}}
    <span class="value-wrapper">
        <span>NPR</span>
        <span class="total-payable-npr" data-appid="{{ $application->id }}">{{ number_format($totalValueNPR, 2) }}</span>
    </span>
</div>
                        <div class="total-row paid-row editable-field"
                            data-appid="{{ $application->id }}" data-field="paid"
                            data-commission-transaction-id="{{ optional($commissionTransaction)->id }}">
                            <span>Paid</span>
                            <div class="paid-controls">
                                <span class="display-value">NPR {{ number_format($paidValue, 2) }}</span>
                                <div class="edit-input" style="display: none;">
                                    <input type="number" name="paid" value="{{ $paidValue }}" step="0.01" min="0" class="edit-field-input">
                                </div>
                            </div>
                        </div>
                        <div class="total-row final">
                            <span>BALANCE DUE: NPR <span class="balance-amount" data-appid="{{ $application->id }}">{{ number_format($balanceDueNPR, 2) }}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="terms-section">
                    <div class="terms-title">TERMS AND CONDITIONS:</div>
                    <div class="terms-text">
                        Payment is due within 5 days from the invoice date.
                    </div>
                    <div class="signature-section">
                        <p>Authorized Signatory</p>
                    </div>
                </div>

                <!-- Bottom curve -->
                <div class="bottom-curve"></div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<style>
    :root {
        --primary-color: #4CAF50;
        --secondary-color: #388E3C;
        --accent-color: #FF9800;
        --light-bg: #F8FBF9;
        --dark-text: #2C3E50;
        --border-color: #DCE7DC;
        --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        font-family: var(--font-family);
        background-color: var(--light-bg);
        color: var(--dark-text);
        margin: 0;
        padding: 0;
    }

    .main-container {
        padding: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: bold;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .card.card-body.mb-4.shadow-sm {
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .invoice-document {
        border: 1px solid #eee;
    }

    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* OPTIMIZED FOR A4 PDF - Reduced heights and spacing */
    .header-bg {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        height: 80px; /* Reduced from 150px */
        position: relative;
        overflow: hidden;
    }

    .header-bg::after {
        content: '';
        position: absolute;
        bottom: -25px; /* Reduced curve */
        left: -50px;
        right: -50px;
        height: 50px; /* Reduced height */
        background: white;
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 2;
        padding: 15px 30px; /* Reduced padding */
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo-section {
        color: white;
    }

    .company-name {
        font-family: "Merriweather", serif;
        font-size: 24px; /* Reduced from 32px */
        font-weight: bold;
        margin-bottom: 3px;
    }

    .invoice-header {
        padding: 0px 30px 15px; /* Reduced padding */
        position: relative;
        z-index: 3;
    }

    .invoice-title {
        font-size: 28px; /* Reduced from 36px */
        font-weight: bold;
        color: var(--secondary-color);
        margin-bottom: 15px; /* Reduced margin */
    }

    .invoice-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px; /* Reduced margin */
    }

    .invoice-info {
        font-size: 13px; /* Slightly reduced */
        line-height: 1.4; /* Tighter line height */
    }

    .invoice-info strong {
        color: var(--secondary-color);
    }

    .billing-section {
        padding: 0 30px 20px; /* Reduced padding */
        display: flex;
        justify-content: space-between;
        gap: 30px;
    }

    .bill-to,
    .payment-info {
        flex: 1;
    }

    .section-title {
        font-size: 14px; /* Reduced from 16px */
        font-weight: bold;
        color: var(--secondary-color);
        margin-bottom: 10px;
    }

    .billing-details,
    .payment-details {
        font-size: 13px; /* Reduced */
        line-height: 1.4;
        color: #333;
    }

    .table-container {
        padding: 0 30px;
        margin-bottom: 20px; /* Reduced margin */
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .invoice-table thead {
        background: var(--secondary-color);
        color: white;
    }

    .invoice-table th {
        padding: 10px 12px; /* Reduced padding */
        text-align: left;
        font-weight: bold;
        font-size: 13px; /* Reduced */
    }

    .invoice-table th:last-child,
    .invoice-table td:last-child {
        text-align: right;
    }

    .invoice-table tbody tr {
        border-bottom: 1px solid #eee;
    }

    .invoice-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .invoice-table td {
        padding: 8px 12px; /* Reduced padding */
        font-size: 13px; /* Reduced */
        color: #333;
    }

    .totals-section {
        padding: 0 30px 25px; /* Reduced padding */
        display: flex;
        justify-content: flex-end;
    }

    .totals-box {
        min-width: 280px; /* Slightly reduced */
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0; /* Reduced padding */
        font-size: 13px; /* Reduced */
        align-items: center;
    }
    
    /* FIX: Prevent wrapping of NPR and amount */
    .total-row.total-npr > span:last-child {
        white-space: nowrap;
    }

    .total-row.subtotal {
        border-bottom: 1px solid #eee;
        margin-bottom: 6px;
    }
    .value-wrapper {
    display: inline-flex; /* Acts like a single inline block */
    align-items: baseline; /* Aligns the text nicely */
    gap: 5px;              /* Creates a consistent space between NPR and the number */
}

    .editable-field .display-value {
        cursor: pointer;
        border-bottom: 1px dotted transparent;
        transition: border-color 0.2s ease;
    }

    .editable-field .display-value:hover {
        border-bottom-color: var(--primary-color);
    }

    .edit-input {
        display: none;
    }

    .edit-field-input {
        width: 100px;
    }

    /* CRITICAL FIX: Force single line for Balance Due */
    .total-row.final {
        background: var(--secondary-color);
        color: white;
        padding: 12px 15px; /* Reduced padding */
        font-size: 16px; /* Reduced from 18px */
        font-weight: bold;
        margin-top: 8px;
        border-radius: 5px;
        text-align: center; /* Center the text */
        display: block !important; /* Override flex to block */
        white-space: nowrap !important; /* Prevent any wrapping */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .total-row.final span {
        white-space: nowrap !important;
        display: inline !important;
    }

    .terms-section {
        padding: 20px 30px; /* Reduced padding */
        border-top: 2px solid var(--secondary-color); /* Thinner border */
        margin-top: auto; /* Pushes this section to the bottom in a flex container */
    }

    .terms-title {
        font-size: 14px; /* Reduced */
        font-weight: bold;
        color: var(--secondary-color);
        margin-bottom: 8px;
    }

    .terms-text {
        font-size: 13px; /* Reduced */
        color: #666;
        line-height: 1.4;
    }

    .signature-section {
        margin-top: 1rem; /* Reduced margin */
    }

    .bottom-curve {
        height: 80px; /* Reduced from 60px */
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        position: relative;
    }

    .bottom-curve::before {
        content: '';
        position: absolute;
        top: -20px; /* Adjusted for smaller curve */
        left: -50px;
        right: -50px;
        height: 40px;
        background: white;
        border-radius: 50%;
    }

    #downloadPDFBtn {
        background-color: var(--primary-color);
        border: none;
        font-weight: 500;
    }

    #downloadPDFBtn:hover {
        background-color: var(--secondary-color);
    }

    /* PDF-specific optimizations */
    .logo-icon img {
        width: 60px !important; /* Smaller logo for PDF */
        height: auto;
    }
</style>

<script>
// Your existing Javascript remains unchanged.
// The fix was purely in the CSS.
document.addEventListener('DOMContentLoaded', function() {
    // Make jsPDF available globally for v2+
    window.jsPDF = window.jspdf.jsPDF;

    // [Keep all your existing editing functionality here - unchanged]
    let currentlyEditing = null;

    function startEditing(container) {
        if (currentlyEditing) {
            Swal.fire({
                title: 'Info',
                text: "Please save or cancel the changes in the current field before editing another.",
                icon: 'info',
                confirmButtonText: 'OK'
            });
            return;
        }

        currentlyEditing = container;
        const displayValue = container.querySelector('.display-value');
        const editInputContainer = container.querySelector('.edit-input');
        const inputField = editInputContainer.querySelector('input.edit-field-input');

        const cleanValue = displayValue.textContent.replace(/[^0-9.-]/g, '');
        inputField.value = cleanValue;
        inputField.dataset.originalValue = cleanValue;

        displayValue.style.display = 'none';
        editInputContainer.style.display = 'inline-block';
        inputField.focus();
        inputField.select();
    }

    function finalizeEdit(revert = false) {
        if (!currentlyEditing) return;

        const container = currentlyEditing;
        const displayValue = container.querySelector('.display-value');
        const editInputContainer = container.querySelector('.edit-input');
        const inputField = editInputContainer.querySelector('input.edit-field-input');

        if (revert) {
            inputField.value = inputField.dataset.originalValue;
        }

        editInputContainer.style.display = 'none';
        displayValue.style.display = 'inline-block';

        currentlyEditing = null;
    }

    document.querySelectorAll('.editable-field .display-value').forEach(display => {
        display.addEventListener('dblclick', function() {
            const container = this.closest('.editable-field');
            startEditing(container);
        });
    });

    document.addEventListener('keydown', function(e) {
        if (!currentlyEditing) return;
        if (e.key === 'Enter') {
            e.preventDefault();
            confirmAndSaveChanges();
        } else if (e.key === 'Escape') {
            finalizeEdit(true);
        }
    });

    document.addEventListener('click', function(event) {
        if (currentlyEditing && !currentlyEditing.contains(event.target)) {
            confirmAndSaveChanges();
        }
    });

    function confirmAndSaveChanges() {
        if (!currentlyEditing) return;

        const container = currentlyEditing;
        const inputField = container.querySelector('input.edit-field-input');
        const fieldName = container.dataset.field.replace('_', ' ');
        const newValue = inputField.value.trim();
        const originalValue = inputField.dataset.originalValue.trim();

        if (newValue === originalValue) {
            finalizeEdit();
            return;
        }

        Swal.fire({
            title: 'Save Changes?',
            text: `Do you want to save the new ${fieldName} value: ${newValue}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const appId = container.dataset.appid;
                const field = container.dataset.field;
                finalizeEdit();
                saveData(appId, field, newValue, originalValue);
            } else {
                finalizeEdit(true);
            }
        });
    }

    function saveData(appId, field, newValue, originalValue) {
        const container = document.querySelector(`.invoice-document[data-appid="${appId}"] .editable-field[data-field="${field}"]`);
        if (!container) {
            showError('UI Error: Could not find the field to update.');
            return;
        }

        const commissionTransactionId = container.dataset.commissionTransactionId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/finance/updateReceivable/${appId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: field,
                    value: newValue,
                    type: 'payable',
                    commission_transaction_id: commissionTransactionId
                })
            })
            .then(handleResponse)
            .then(data => {
                if (data.success) {
                    const displayValue = container.querySelector('.display-value');
                    const inputField = container.querySelector('input.edit-field-input');
                    const prefix = (field === 'exchange_rate' ? 'NPR ' : 'NPR ');
                    const formattedValue = parseFloat(data.value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    displayValue.textContent = `${prefix}${formattedValue}`;
                    inputField.dataset.originalValue = data.value;

                    updateCalculations(appId);

                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'Update successful.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'The server reported an issue.');
                }
            })
            .catch(error => {
                console.error('Update error:', error);
                showError(error.message);
                const displayValue = container.querySelector('.display-value');
                const prefix = (field === 'exchange_rate' ? 'NPR ' : 'NPR ');
                const formattedValue = parseFloat(originalValue).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                displayValue.textContent = `${prefix}${formattedValue}`;
                updateCalculations(appId);
            });
    }

    function handleResponse(response) {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || `HTTP error! Status: ${response.status}`)
            });
        }
        return response.json();
    }

    function showError(message) {
        Swal.fire({
            title: 'Error!',
            html: `<p>${message}</p><p class="small text-muted mt-2">Please try again or contact support.</p>`,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    function updateCalculations(appId) {
        const invoiceDoc = document.querySelector(`.invoice-document[data-appid="${appId}"]`);
        if (!invoiceDoc) return;

        const bonus = parseFloat(invoiceDoc.dataset.bonus || 0);
        const incentive = parseFloat(invoiceDoc.dataset.incentive || 0);
        const progressive = parseFloat(invoiceDoc.dataset.progressive || 0);
        const commissionAmountText = invoiceDoc.querySelector('.invoice-table tbody tr:first-child td:last-child').textContent;
        const commissionAmount = parseFloat(commissionAmountText.replace(/[^0-9.-]/g, '') || 0);

        const exchangeRateText = invoiceDoc.querySelector(`[data-field="exchange_rate"] .display-value`).textContent;
        const exchangeRate = parseFloat(exchangeRateText.replace(/[^0-9.-]/g, '')) || 1;

        const paidText = invoiceDoc.querySelector(`[data-field="paid"] .display-value`).textContent;
        const paid = parseFloat(paidText.replace(/[^0-9.-]/g, '')) || 0;

        const subtotal = commissionAmount + bonus + incentive + progressive;
        const totalValueNPR = subtotal * exchangeRate;
        const balanceDueNPR = Math.max(0, totalValueNPR - paid);

        const totalPayableElement = invoiceDoc.querySelector(`.total-payable-npr`);
        const balanceDueElement = invoiceDoc.querySelector(`.balance-amount`);

        if (totalPayableElement) totalPayableElement.textContent = totalValueNPR.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        if (balanceDueElement) balanceDueElement.textContent = balanceDueNPR.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ===================================================================
    // == PERFECT A4 PDF GENERATION - NO GAPS!
    // ===================================================================
    const downloadBtn = document.getElementById('downloadPDFBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', async () => {
            const allDocuments = document.querySelectorAll('.invoice-document');
            if (allDocuments.length === 0) {
                Swal.fire('No Documents Found', 'There is nothing to download.', 'info');
                return;
            }

            Swal.fire({
                title: 'Generating Perfect A4 PDF...',
                text: 'Creating pixel-perfect layout. Please wait.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                await document.fonts.ready;
            } catch (err) {
                console.warn('Font loading check failed, proceeding anyway.', err);
            }

            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = 210;
            const pdfHeight = 297;

            try {
                for (let i = 0; i < allDocuments.length; i++) {
                    const originalDocElement = allDocuments[i];
                    
                    // Create a temporary, off-screen container with perfect A4 dimensions
                    const renderContainer = document.createElement('div');
                    Object.assign(renderContainer.style, {
                        position: 'fixed',
                        top: '-10000px', // Hide it from view
                        left: '0',
                        width: `${pdfWidth}mm`,
                        height: `${pdfHeight}mm`,
                        backgroundColor: 'white',
                        margin: '0',
                        padding: '0',
                        boxSizing: 'border-box',
                        overflow: 'hidden'
                    });

                    const clone = originalDocElement.cloneNode(true);

                    // Force the cloned document to be a flex container that fills the A4 height
                    Object.assign(clone.style, {
                        width: '100%',
                        height: '100%',
                        minHeight: '100%',
                        maxHeight: '100%',
                        margin: '0',
                        padding: '0',
                        boxSizing: 'border-box',
                        display: 'flex',
                        flexDirection: 'column',
                        overflow: 'hidden',
                        backgroundColor: 'white'
                    });
                    
                    renderContainer.appendChild(clone);
                    document.body.appendChild(renderContainer);
                    
                    // Allow a brief moment for the browser to render the new layout
                    await new Promise(resolve => setTimeout(resolve, 300));

                    // Generate canvas from the perfectly sized container
                    const canvas = await html2canvas(renderContainer, {
                        scale: 3, // Increase scale for higher resolution/quality
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        scrollX: 0,
                        scrollY: 0,
                        windowWidth: renderContainer.scrollWidth,
                        windowHeight: renderContainer.scrollHeight
                    });

                    document.body.removeChild(renderContainer);
                    
                    const imgData = canvas.toDataURL('image/png', 1.0);

                    if (i > 0) pdf.addPage();
                    
                    // Add the image to the PDF, ensuring it fits the A4 page perfectly
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                }
                
                pdf.save(`WRI-Accounts-Payable-${new Date().toISOString().slice(0,10)}.pdf`);
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Your PDF has been generated perfectly.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

            } catch (error) {
                console.error('PDF generation failed:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to generate PDF. Please check the console for errors.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } finally {
                Swal.close();
            }
        });
    }
});
</script>
@endsection