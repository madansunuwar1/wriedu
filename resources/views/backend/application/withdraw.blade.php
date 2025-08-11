@extends('layouts.admin')

@include('backend.script.session')


@include('backend.script.alert')


@section('content')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }

        thead th {
            background-color: #f4f4f4;
            color: #333;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 12px;
            text-align: left;
        }
    </style>

    <h2>Withdrawn Applications</h2>

    <table id="withdrawnTable">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>University</th>
                <th>Course</th>
                <th>Intake</th>
                <th>Application Status</th>
                <th>Date Withdrawn</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($forms as $form)
                <tr>
                    <td>{{ $form->name ?? 'N/A' }}</td>
                    <td>{{ $form->university ?? 'N/A' }}</td>
                    <td>{{ $form->course ?? 'N/A' }}</td>
                    <td>{{ $form->intake ?? 'N/A' }}</td>
                    <td>{{ $form->document ?? 'N/A' }}</td>
                    
                </tr>
            @endforeach

            @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->name ?? 'N/A' }}</td>
                    <td>{{ $application->customSearchField ?? 'N/A' }}</td>
                    <td>{{ $application->courseSearchField ?? 'N/A' }}</td>
                    <td>{{ $application->searchField ?? 'N/A' }}</td>
                    <td>N/A</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection