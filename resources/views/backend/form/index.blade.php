@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <style>
        /* Container for better padding on different screens */
        .container {
            padding: 15px;
            max-width: 100%;
            overflow-x: auto;
        }

        /* General Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
            min-width: 1000px;
        }

        thead th {
            background-color: #f4f4f4;
            color: #333;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            white-space: nowrap;
        }

        tbody td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Responsive Header Styles */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            gap: 20px;
        }

        .heading {
            font-size: clamp(24px, 5vw, 40px);
            color: green;
            margin: 0;
            flex: 1;
            text-align: left;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Button Styles */
        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            min-width: 120px;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            background-color: #28a745;
            color: white;
            border: 1px solid #28a745;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-download:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            min-width: auto;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Alert Styles */
        .alert {
            margin: 15px;
            padding: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Table Responsive Wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .heading {
                text-align: center;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .container {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .btn-download {
                width: 100%;
            }
            
            .header-actions {
                flex-direction: column;
            }
        }
    </style>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="heading">Form Data</h1>
            <div class="header-actions">
                <a href="{{ route('backend.form.create') }}" class="btn-download">Add Application Form</a>
                <button id="download-excel" class="btn-download">Download Excel</button>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Last Qualification</th>
                        <th>Passed</th>
                        <th>GPA</th>
                        <th>English</th>
                        <th>English Test</th>
                        <th>Higher</th>
                        <th>Less</th>
                        <th>Score</th>
                        <th>English Score</th>
                        <th>English Theory</th>
                        <th>Country</th>
                        <th>Location</th>
                        <th>University</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th>Document</th>
                        <th>Additional Info</th>
                        <th>Source</th>
                        <th>Partner Details</th>
                        <th>Other Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->name }}</td>
                            <td>{{ $application->last }}</td>
                            <td>{{ $application->email }}</td>
                            <td>{{ $application->phone }}</td>
                            <td>{{ $application->lastqualification }}</td>
                            <td>{{ $application->passed }}</td>
                            <td>{{ $application->gpa }}</td>
                            <td>{{ $application->english }}</td>
                            <td>{{ $application->englishTest }}</td>
                            <td>{{ $application->higher }}</td>
                            <td>{{ $application->less }}</td>
                            <td>{{ $application->score }}</td>
                            <td>{{ $application->englishscore }}</td>
                            <td>{{ $application->englishtheory }}</td>
                            <td>{{ $application->country }}</td>
                            <td>{{ $application->location }}</td>
                            <td>{{ $application->university }}</td>
                            <td>{{ $application->course }}</td>
                            <td>{{ $application->intake }}</td>
                            <td>{{ $application->document }}</td>
                            <td>{{ $application->additionalinfo }}</td>
                            <td>{{ $application->source }}</td>
                            <td>{{ $application->partnerDetails }}</td>
                            <td>{{ $application->otherDetails }}</td>
                            <td>
                            <a href="{{ route('application.edit', $application->id) }}">Edit</a>
                                <form action="{{ route('application.destroy', $application->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDelete()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-download btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this record?');
        }
    </script>
@endsection