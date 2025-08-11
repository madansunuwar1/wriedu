<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application data</title>

    @include('backend.script.session')

    @include('backend.script.alert')

    
    <style>
        /* Style for the table container */
        table {
            width: 80%; /* Reduce the table width */
            border-collapse: collapse;
            margin: 20px auto; /* Center the table */
            font-family: Arial, sans-serif;
            font-size: 14px; /* Smaller font size */
        }

        /* Header row styles */
        thead th {
            background-color: #f4f4f4;
            color: #333;
            padding: 8px; /* Smaller padding */
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        /* Data row styles */
        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 8px; /* Smaller padding */
            text-align: left;
        }

        /* Button styles */
        .link-btn {
            display: inline-block;
            padding: 6px 10px; /* Smaller button padding */
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid transparent;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }

        .link-btn:hover {
            background-color: #f0f0f0;
            border-color: #0056b3;
            color: #0056b3;
        }

        /* Alert box styles */
        .alert {
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                
                <th>Country</th>
                <th>Intake</th>
                <th>University</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $application)
                <tr>
                    
                    <td>{{ $application->country }}</td>
                    <td>{{ $application->searchField }}</td>
                    <td>{{ $application->customSearchField }}</td>
                    <td>{{ $application->courseSearchField }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
