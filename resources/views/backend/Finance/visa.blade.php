@extends('layouts.admin')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Information</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    @include('backend.script.session')
    @include('backend.script.alert')
</head>
<body>
    <div class="my-5">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Visa and Finance Information</h2>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>University</th>
                                <th>Intake</th>
                                <th>Fees Paid</th>
                                <th>Commission Fees (%)</th>
                                <th>Total Fees</th>
                                <th>Total Commission Pay</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $index => $application)
                                @php
                                    $matchedFinance = $finances->firstWhere('university', $application->university);
                                    
                                    // Convert values to float and handle percentage calculation
                                    $commissionPercentage = $matchedFinance && is_numeric($matchedFinance->commission) ? (float)$matchedFinance->commission : 0;
                                    $feesPaid = is_numeric($application->fee) ? (float)$application->fee : 0;
                                    
                                    // Calculate commission as percentage of fees paid
                                    $totalCommissionPay = ($commissionPercentage > 0 && $feesPaid > 0) ? ($feesPaid * $commissionPercentage / 100) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $application->name }}</td>
                                    <td>{{ $application->university }}</td>
                                    <td>{{ $application->intake }}</td>
                                    <td>{{ $application->fee }}</td>
                                    <td>{{ $matchedFinance && is_numeric($matchedFinance->commission) ? $matchedFinance->commission : 'N/A' }}</td>
                                    <td>{{ isset($finances[$index]->total) ? $finances[$index]->total : 'N/A' }}</td>
                                    <td>{{ $totalCommissionPay > 0 ? number_format($totalCommissionPay, 2) : 'N/A' }}</td>
                                    <td>{{ isset($finances[$index]->comment) ? $finances[$index]->comment : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
@endsection