@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')


@section('content')
    <style>
        table {
            width: 40%;
            border-collapse: collapse;
            margin: 20px auto; /* Center the table horizontally */
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

        .heading {
            text-align: center;
            font-size: 40px;
            color: green;
        }

        .link-btn {
            display: inline-block;
            padding: 8px 12px;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .link-btn:hover {
            background-color: #f0f0f0;
            color: #0056b3;
        }

        /* Success message styling */
        .alert-success {
            background-color: #d4edda; /* Light green */
            color: #155724; /* Dark green for text */
            border: 1px solid #c3e6cb; /* Border color */
            padding: 12px;
            border-radius: 4px;
            width: 40%;
            margin: 20px auto; /* Center the message */
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .link-btn {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    color: #fff;
    background-color: green;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
}

.link-btn i {
    margin-right: 6px;
}

    </style>

    <h1 class="heading">Index Comment</h1>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Comment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lead_comments as $lead_comment)
                <tr>
                    <td>{{ $lead_comment->id }}</td>
                    <td>{{ $lead_comment->comment }}</td>
                    <td>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

                    <a href="{{ route('lead_comment.edit', $lead_comment->id) }}" class="link-btn">
                    <i class="fas fa-pencil-alt"></i> Edit
                    </a>

                    

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endsection
