@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="bg-white">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Georgia', serif;
            background-color: white;
            padding: 40px 20px;
            color: #2c3e50;
        }

        .container {
            margin: 0 auto;
        }

        form {
            background-color: #ffffff;
            border-radius: 12px;
        }

        h1 {
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
            position: relative;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .form-container {
            font-family: Arial, sans-serif;
            max-width: 1600px;
            margin: 0 auto;
            border-radius: 8px;
        }

        .flex-section {
            flex: 1;
            min-width: 250px;
        }

        .dropdown-container {
            position: relative;
            margin-bottom: 20px;
        }

        .dropdown-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            background-color: #fff;
            cursor: pointer;
        }

        .dropdown-input:focus {
            outline: none;
            border-color: #60a5fa;
        }

        .dropdown-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .dropdown-list.show {
            display: block;
        }

        .dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .dropdown-item input {
            margin-right: 8px;
        }

        .document-list {
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-top: 8px;
            height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .document-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .selected-document {
            background-color: #f0fdf4;
        }

        .unselected-document {
            background-color: #fef2f2;
        }

        .document-item .icon {
            font-size: 18px;
        }

        .selected-document .icon {
            color: #22c55e;
        }

        .unselected-document .icon {
            color: #ef4444;
        }

        .document-item button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .selected-document button {
            color: #ef4444;
        }

        .unselected-document button {
            color: #22c55e;
        }

        h3,
        label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
        }

        @media (max-width: 768px) {
            .flex-section {
                flex: 1 1 100%;
            }
        }

        .section-title {
            font-size: 24px;
            color: #2e7d32;
            margin: 35px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #81c784;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .form-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            color: #2e7d32;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            color: #2c3e50;
            border-radius: 6px;
            border: 1.5px solid #81c784;
            transition: all 0.3s ease;
            font-family: 'Georgia', serif;
            background-color: #fcfcfc;
        }

        .form-group textarea {
            width: 490px;
            min-height: 120px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .education-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .education-table th,
        .education-table td {
            border: 1px solid #e8f5e9;
            padding: 15px 20px;
            text-align: left;
            font-size: 15px;
        }

        .education-table th {
            background-color: #2e7d32;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.8px;
        }

        .education-table tr:hover {
            background-color: #f1f8e9;
        }

        .education-table td input {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #81c784;
            border-radius: 4px;
            font-size: 15px;
            font-family: 'Georgia', serif;
        }

        .education-table td input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-submit {
            background-color: #2e7d32;
            color: #fff;
            border: none;
            padding: 16px 35px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            margin: 40px auto 0;
            min-width: 220px;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-family: 'Georgia', serif;
        }

        .btn-submit:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            min-width: 300px;
        }

        .popup-content {
            margin-bottom: 20px;
        }

        .popup h2 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .popup p {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .popup-btn {
            background-color: #2e7d32;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Georgia', serif;
        }

        .popup-btn:hover {
            background-color: #1b5e20;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .error {
            border-color: red;
        }

        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 20px;
            }

            form {
                padding: 25px;
            }

            .education-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            h1 {
                font-size: 28px;
            }

            .section-title {
                font-size: 22px;
            }
        }

        .form-container {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        .preview-container {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            background-color: #d1d5db;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        button:hover {
            background-color: #0056b3;
        }

        button.prev {
            background-color: #6c757d;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .preview-section {
            margin-bottom: 20px;
        }

        .preview-section h3 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .preview-data {
            margin-left: 10px;
        }

        .preview-data p {
            margin: 5px 0;
            color: #666;
        }

        .preview-data span {
            font-weight: bold;
            color: #333;
        }

        .progress-bar1 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 30px;
            margin: 0 auto;
        }

        .progress-bar1::before {
            content: '';
            background-color: #ddd;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 4px;
            width: 100%;
            z-index: 0;
        }

        .progress-step {
            background-color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #999;
            position: relative;
            z-index: 1;
        }

        .progress-step.active {
            border-color: #007bff;
            color: #007bff;
        }

        .progress-step.completed {
            border-color: #28a745;
            background-color: #28a745;
            color: white;
        }

        .step-label {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 12px;
            color: #666;
        }

        .progress-container {
            margin-bottom: 100px;
            margin-left: 20px;
            margin-right: 20px;
        }

        .university-entry {
            padding: 10px;
            border-radius: 5px;
        }

        .title {
            color: #2e7d32;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
            top: 0;
        }

        .form-preview {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .preview-container {
            height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
        }

        .section {
            padding-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #81c784;
        }

        .section:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .info-item {
            padding: 0.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .info-value {
            color: #2c3e50;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .not-filled {
            color: #95a5a6;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            body {
                padding: 1rem;
            }
        }

        .preview-container::-webkit-scrollbar {
            width: 8px;
        }

        .preview-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .preview-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .preview-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .flex-container {
            display: flex;
        }

        .document-list {
            min-height: 100px;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background-color: #f8fafc;
            margin-bottom: 1rem;
        }

        .document-item {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .document-item:hover {
            background-color: #f1f5f9;
        }

        .document-checkbox {
            margin-right: 8px;
        }

        .document-error {
            color: #ef4444;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .document-message {
            color: #6b7280;
            padding: 0.5rem;
            font-size: 0.875rem;
            font-style: italic;
        }

        #selectedDocuments .document-item {
            background-color: #dcfce7;
            border-color: #86efac;
        }

        #selectedDocuments .document-item:hover {
            background-color: #bbf7d0;
        }

        .dropdown-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .dropdown-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background-color: #ffffff;
            cursor: pointer;
        }

        .dropdown-list {
            position: absolute;
            width: 100%;
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background-color: white;
            z-index: 10;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .dropdown-item {
            padding: 0.5rem;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
        }

        .dropdown-item label {
            margin-left: 8px;
            cursor: pointer;
            flex-grow: 1;
        }

        .dropdown-message {
            padding: 0.5rem;
            color: #6b7280;
            font-style: italic;
        }

        .flex-section {
            flex: 1;
            min-width: 250px;
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #2e7d32;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #81c784;
        }

        .toggle-checkbox {
            transition: right 0.2s ease-in-out;
        }
    </style>

    <div class="rounded-md mx-auto mb-4 overflow-hidden">
        <form id="studentForm" action="{{ route('backend.leadform.store') }}" method="POST" novalidate>
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <div class="d-flex gap-2">
                <div class="form-container">
                    <h1 class="mb-2 text-[#2e7d32]">Lead Fill Form</h1>

                    <div style="display: flex; align-items: center; justify-content: flex-end; margin-bottom: 16px; gap: 8px;">
                        <label id="formModeLabel" for="rawModeToggle" style="font-size: 14px; font-weight: 500; color: #374151;">Full Submit</label>
                        <div style="position: relative; display: inline-block; width: 40px; margin-right: 8px; vertical-align: middle;">
                            <input
                                type="checkbox"
                                name="rawModeToggle"
                                id="rawModeToggle"
                                style="
                position: absolute;
                width: 24px;
                height: 24px;
                background-color: #ffffff;
                border: 2px solid #d1d5db;
                border-radius: 9999px;
                cursor: pointer;
                appearance: none;
                transition: transform 0.3s ease-in-out;
            "
                                onclick="this.style.transform = this.checked ? 'translateX(16px)' : 'translateX(0)'" />
                            <label
                                for="rawModeToggle"
                                style="
                display: block;
                overflow: hidden;
                height: 24px;
                border-radius: 9999px;
                background-color: #d1d5db;
                cursor: pointer;
                transition: background-color 0.3s ease;
            "></label>
                        </div>
                    </div>


                    <div class="progress-container">
                        <div class="progress-bar1">
                            <div class="progress-step active" data-step="1">1<span class="step-label">Personal Info</span></div>
                            <div class="progress-step" data-step="2">2<span class="step-label">Study Info</span></div>
                            <div class="progress-step" data-step="3">3<span class="step-label">Test Info</span></div>
                            <div class="progress-step" data-step="4">4<span class="step-label">source info</span></div>
                            <div class="progress-step" data-step="5">5<span class="step-label">university Info</span></div>
                            <div class="progress-step" data-step="6">6<span class="step-label">Document Info</span></div>
                        </div>
                    </div>

                    <div class="form-section active" id="section1">
                        <div class="section-title">Personal Information</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="block text-gray-700 font-semibold mb-2">Name:</label>
                                <input type="text" id="name" name="name" class="" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="block text-gray-700 font-semibold mb-2">Email:</label>
                                <input type="email" id="email" name="email" class="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" class="" required>
                            </div>
                            <div class="form-group">
                                <label for="locations" class="block text-gray-700 font-semibold mb-2">Adress:</label>
                                <input type="text" id="locations" name="locations" class="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="link" class="block text-gray-700 font-semibold mb-2">Link:</label>
                                <input type="text" id="link" name="link" class="">
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="section2">
                        <div class="section-title">Study Information</div>
                        <div class="form-row mt-4">
                            <div class="form-group">
                                <label for="lastqualification" class="block text-gray-700 font-semibold mb-2">Level:</label>
                                <select id="lastqualification" name="lastqualification" class="">
                                    <option value="" disabled selected>Select Last Level</option>
                                    <option value="Intermediate/Diploma">Intermediate/Diploma</option>
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Masters">Masters</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courselevel" class="block text-gray-700 font-semibold mb-2"> Course Name :</label>
                                <input type="text" id="courselevel" name="courselevel" class="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="passed" class="block text-gray-700 font-semibold mb-2">Pass Year:</label>
                                <select id="passed" name="passed" class="">
                                    <option value="" disabled selected>Select Passed Year</option>
                                </select>
                            </div>
                            <script>
                                const currentYear = new Date().getFullYear();
                                const selectElement = document.getElementById('passed');
                                const startYear = 1900;
                                for (let year = currentYear; year >= startYear; year--) {
                                    const option = document.createElement('option');
                                    option.value = year;
                                    option.textContent = year;
                                    selectElement.appendChild(option);
                                }
                            </script>
                            <div class="form-group">
                                <label for="gpa" class="block text-gray-700 font-semibold mb-2">GPA / Percentage:</label>
                                <input type="text" id="gpa" name="gpa" class="" placeholder="GPA / Percentage">
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="section3">
                        <div class="section-title">Test Information</div>
                        <div class="form-group">
                            <div class="flex flex-col gap-4">
                                <div class="w-full">
                                    <label for="englishTest" class="block text-gray-700 font-semibold mb-2">English Language Test:</label>
                                    <select id="englishTest" name="englishTest" class="">
                                        <option value="" disabled selected>Select English Test</option>
                                        <option value="IELTS">IELTS</option>
                                        <option value="PTE">PTE</option>
                                        <option value="ELLT">ELLT</option>
                                        <option value="No Test">No Test</option>
                                        <option value="Duolingo">Duolingo</option>
                                        <option value="MOI">MOI</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div id="test-fields" class="grid grid-cols-3 gap-4" style="display:none;">
                                    <div>
                                        <label for="higher" class="block text-gray-700 font-semibold mb-2">Overall Higher:</label>
                                        <input type="text" id="higher" name="higher" class="" placeholder="Enter Overall Higher" />
                                    </div>
                                    <div>
                                        <label for="less" class="block text-gray-700 font-semibold mb-2">Not Less than:</label>
                                        <input type="text" id="less" name="less" class="" placeholder="Enter Not Less than" />
                                    </div>
                                    <div>
                                        <label for="score" class="block text-gray-700 font-semibold mb-2">Overall Score:</label>
                                        <input type="text" id="score" name="score" class="" placeholder="Calculated score" disabled />
                                    </div>
                                </div>
                                <div id="no-test-fields" class="grid grid-cols-2 gap-4" style="display:none;">
                                    <div>
                                        <label for="englishscore" class="block text-gray-700 font-semibold mb-2">Overall English Score:</label>
                                        <input type="text" id="englishscore" name="englishscore" class="" placeholder="Enter English Score" />
                                    </div>
                                    <div>
                                        <label for="englishtheory" class="block text-gray-700 font-semibold mb-2">English Theory:</label>
                                        <input type="text" id="englishtheory" name="englishtheory" class="" placeholder="Enter English Theory" />
                                    </div>
                                </div>
                                <div id="other-field" class="w-full" style="display:none;">
                                    <label for="otherScore" class="block text-gray-700 font-semibold mb-2">Other Test Score:</label>
                                    <input type="text" id="otherScore" name="otherScore" class="" placeholder="Enter Test Score" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const englishTestSelect = document.getElementById('englishTest');
                            const testFields = document.getElementById('test-fields');
                            const noTestFields = document.getElementById('no-test-fields');
                            const otherField = document.getElementById('other-field');
                            const higherInput = document.getElementById('higher');
                            const lessInput = document.getElementById('less');
                            const scoreField = document.getElementById('score');

                            const previewTest = document.getElementById('previewTest');
                            const previewFields = document.getElementById('preview-fields');
                            const previewNoTest = document.getElementById('preview-no-test');
                            const previewOther = document.getElementById('preview-other');

                            function resetFields() {
                                higherInput.value = '';
                                lessInput.value = '';
                                scoreField.value = '';
                                document.getElementById('otherScore').value = '';
                                document.getElementById('englishscore').value = '';
                                document.getElementById('englishtheory').value = '';
                            }

                            function hideAllFields() {
                                testFields.style.display = 'none';
                                noTestFields.style.display = 'none';
                                otherField.style.display = 'none';
                                if (previewFields) previewFields.classList.add('hidden');
                                if (previewNoTest) previewNoTest.classList.add('hidden');
                                if (previewOther) previewOther.classList.add('hidden');
                            }

                            englishTestSelect.addEventListener('change', function() {
                                resetFields();
                                hideAllFields();

                                if (previewTest) previewTest.textContent = this.value || '-';
                                switch (this.value) {
                                    case 'IELTS':
                                    case 'PTE':
                                    case 'ELLT':
                                    case 'Duolingo':
                                        testFields.style.display = 'grid';
                                        if (previewFields) previewFields.classList.remove('hidden');
                                        break;
                                    case 'No Test':
                                        noTestFields.style.display = 'grid';
                                        if (previewNoTest) previewNoTest.classList.remove('hidden');
                                        break;
                                    case 'Other':
                                        otherField.style.display = 'block';
                                        if (previewOther) previewOther.classList.remove('hidden');
                                        break;
                                }
                            });

                            [higherInput, lessInput].forEach(input => {
                                input.addEventListener('input', function() {
                                    const higherValue = higherInput.value.trim();
                                    const lessValue = lessInput.value.trim();
                                    const previewScoreEl = document.getElementById('previewScore');

                                    if (higherValue && lessValue) {
                                        if (!isNaN(higherValue) && !isNaN(lessValue)) {
                                            scoreField.value = `${higherValue}/${lessValue}`;
                                            if (previewScoreEl) previewScoreEl.textContent = `${higherValue}/${lessValue}`;
                                        } else {
                                            scoreField.value = 'Invalid input';
                                            if (previewScoreEl) previewScoreEl.textContent = 'Invalid input';
                                        }
                                    } else {
                                        scoreField.value = '';
                                        if (previewScoreEl) previewScoreEl.textContent = '-';
                                    }
                                });
                            });
                        });

                        function updatePreview(inputId, previewId) {
                            const inputEl = document.getElementById(inputId);
                            if (inputEl) {
                                inputEl.addEventListener('input', function() {
                                    const previewEl = document.getElementById(previewId);
                                    if (previewEl) previewEl.textContent = this.value || '-';
                                });
                            }
                        }
                        updatePreview('higher', 'previewHigher');
                        updatePreview('less', 'previewLess');
                        updatePreview('score', 'previewScore');
                        updatePreview('englishscore', 'previewEnglishScore');
                        updatePreview('englishtheory', 'previewEnglishTheory');
                        updatePreview('otherScore', 'previewOther');
                    </script>

                    <div class="form-section" id="section4">
                        <div class="section-title">Source Information</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="created_by" class="block text-gray-700 font-semibold mb-2">Source:</label>
                                <select id="created_by" name="created_by" class="form-select">
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ Auth::check() && Auth::id() == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} {{ $user->last }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 form-group">
                                <label for="source" class="block text-gray-700 font-semibold mb-2">Source of Referral:</label>
                                <select id="source" name="source" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>Select Source of Referral</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="partners">Partners</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div id="partnerField" class="flex-1 hidden">
                                <label for="partnerDetails" class="block text-gray-700 font-semibold mb-2">Partner Details:</label>
                                <input type="text" id="partnerDetails" name="partnerDetails" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter partner details" list="partnerDetailsList">
                                <datalist id="partnerDetailsList">
                                    @foreach ($applications as $application)
                                    <option value="{{ $application->otherDetails }}">{{ $application->otherDetails }}</option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div id="otherField" class="flex-1 hidden">
                                <label for="otherDetails" class="block text-gray-700 font-semibold mb-2">Other Referral Details:</label>
                                <input type="text" id="otherDetails" name="otherDetails" class="w-full p-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter other details">
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const sourceDropdown = document.getElementById('source');
                                const partnerField = document.getElementById('partnerField');
                                const otherField = document.getElementById('otherField');

                                function toggleReferralFields() {
                                    const selectedValue = sourceDropdown ? sourceDropdown.value : '';
                                    if (partnerField) partnerField.classList.toggle('hidden', selectedValue !== 'partners');
                                    if (otherField) otherField.classList.toggle('hidden', selectedValue !== 'other');
                                }

                                if (sourceDropdown) {
                                    sourceDropdown.addEventListener('change', toggleReferralFields);
                                    toggleReferralFields();
                                }

                            });
                        </script>
                    </div>

                    <div class="form-section" id="section5">
                        <div class="section-title">University Information</div>
                        <div class="form-row">
                            <div class="flex-1 mt-4 form-group">
                                <label for="country" class="block text-gray-700 font-semibold mb-2">Country:</label>
                                <select id="country" name="country" class="" onchange="handleCountryChange()">
                                    <option value="" disabled selected>Select Country</option>
                                </select>
                            </div>
                            <div class="flex-1 mt-4 form-group">
                                <label for="location" class="block text-gray-700 font-semibold mb-2">Location:</label>
                                <select id="location" name="location" class="" onchange="handleLocationChange()">
                                    <option value="" disabled selected>Select Location</option>
                                </select>
                            </div>
                        </div>
                        <div id="form-container" style="margin-top:25px; display: flex; flex-direction: column; height: 100%;">
                            <div id="form-rows" style="flex-grow: 1;">
                                <div class="flex space-x-2" id="form-row-1">
                                    <div id="university-field-1" class="flex-1 form-group">
                                        <label for="university1" class="block text-gray-700 font-semibold mb-2">University:</label>
                                        <select id="university1" name="university[]" class="" onchange="handleUniversityChange()">
                                            <option value="" disabled selected>Select University</option>
                                        </select>
                                    </div>
                                    <div id="course-field-1" class="flex-1 form-group">
                                        <label for="course1" class="block text-gray-700 font-semibold mb-2">Course:</label>
                                        <select id="course1" name="course[]" class="" onchange="handleCourseChange()">
                                            <option value="" disabled selected>Select Course</option>
                                        </select>
                                    </div>
                                    <div id="intake-field-1" class="flex-1 form-group">
                                        <label for="intake1" class="block text-gray-700 font-semibold mb-2">Intake:</label>
                                        <select id="intake1" name="intake[]" class="">
                                            <option value="" disabled selected>Select Intake</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="section6">
                        <div class="section-title">Document Information</div>
                        <div class="mt-4">
                            <div class="dropdown-container form-group">
                                <label for="academic" class="block font-semibold mb-2">Document Status:</label>
                                <input type="text" id="academic" name="academic_display" class="dropdown-input" placeholder="Select Documents" readonly>
                                <input type="hidden" id="academicValue" name="academic" value="">
                                <div class="dropdown-list" id="dropdownList"></div>
                            </div>
                            <div class="flex-container">
                                <div class="flex-section">
                                    <h3 class="block text-[16px] text-green-700 mb-2.5 font-semibold tracking-[0.2px]">Fully Received</h3>
                                    <div id="selectedDocuments" class="document-list"></div>
                                </div>
                                <div class="flex-section">
                                    <h3 class="block text-[16px] text-green-700 mb-2.5 font-semibold tracking-[0.2px]">Document </h3>
                                    <div id="unselectedDocuments" class="document-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" class="prev" onclick="prevStep()" style="display: none;">Previous</button>
                        <button type="button" class="next bg-[#2e7d32]" onclick="nextStep()">Next</button>
                    </div>
                    <div class="text-center">
                        <button type="submit" id="rawSubmitButton" class="btn-submit" style="display: none;">Submit Raw Data</button>
                    </div>
                </div>

                <div class="preview-container relative">
                    <div class="form-preview">
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-personal-info">Personal Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Name</div>
                                    <div class="info-value"><span class="not-filled" id="preview-name">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><span class="not-filled" id="preview-email">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value"><span class="not-filled" id="preview-phone">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Address</div>
                                    <div class="info-value"><span class="not-filled" id="preview-locations">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Link</div>
                                    <div class="info-value"><span class="not-filled" id="preview-link">Not filled</span></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-study-info">Study Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Level</div>
                                    <div class="info-value"><span class="not-filled" id="preview-lastqualification">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Course Level</div>
                                    <div class="info-value"><span class="not-filled" id="preview-courselevel">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Pass Year</div>
                                    <div class="info-value"><span class="not-filled" id="preview-passed">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">GPA/Percentage</div>
                                    <div class="info-value"><span class="not-filled" id="preview-gpa">Not filled</span></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-test-info">Test Information</p>
                        <div class="section">
                            <div class="preview-section info-grid">
                                <div class="info-item">
                                    <div class="info-label">Selected Test:</div>
                                    <div class="info-value"> <span class="not-filled" id="previewTest">-</span></div>
                                </div>
                                <div id="preview-fields" class=" hidden ">
                                    <div class="info-item">
                                        <div class="info-label">Overall Score:</div>
                                        <div class="info-value"><span class="not-filled" id="previewScore">-</span></div>
                                    </div>
                                </div>
                                <div id="preview-no-test" class=" hidden">
                                    <div class="info-item">
                                        <div class="info-label">English Score:</div>
                                        <div class="info-value"><span class="not-filled" id="previewEnglishScore">-</span></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">English Theory:</div>
                                        <div class="info-value"><span class="not-filled" id="previewEnglishTheory">-</span></div>
                                    </div>
                                </div>
                                <div id="preview-other" class=" hidden">
                                    <div class="info-label">Other Test Score:</div>
                                    <div class="info-value"><span class="not-filled" id="previewOther">-</span></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-document-info">Document Information</p>
                        <div class="section">
                            <div class="info-item">
                                <div class="info-label">Documents received:</div>
                                <div class="info-value"><span class="not-filled" id="preview-documents">Not filled</span></div>
                            </div>
                        </div>
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-university-info">University Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Country:</div>
                                    <div class="info-value"> <span class="not-filled" id="preview-country">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Location: </div>
                                    <div class="info-value"><span class="not-filled" id="preview-location">Not filled</span></div>
                                </div>
                            </div>
                            <div id="preview-universities" class="mt-4"></div>
                        </div>
                        <p class="text-[#2e7d32] text-[0.9rem] font-bold" id="preview-source-info">Source Information</p>
                        <div class="section">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Source</div>
                                    <div class="info-value"><span class="not-filled" id="preview-sources">Not filled</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Source of Referral</div>
                                    <div class="info-value"><span class="not-filled" id="preview-source">Not filled</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class=" bg-[#2e7d32] text-[10px] w-full mt-4" id="download-pdf"> Download </button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let currentStep = 1;
        const totalSteps = document.querySelectorAll(".form-section").length;
        let formCounter = 1;
        let isRawMode = false;
        window.selectedDocsMap = new Map();

        const previewSections = ["preview-personal-info", "preview-study-info", "preview-test-info", "preview-source-info", "preview-university-info", "preview-document-info"];

        const rawModeToggle = document.getElementById('rawModeToggle');
        const progressContainer = document.querySelector('.progress-container');
        const multiStepButtons = document.querySelector('.button-group');
        const rawSubmitButton = document.getElementById('rawSubmitButton');
        const formSections = document.querySelectorAll('.form-section');
        const previewContainer = document.querySelector('.preview-container');

        function updateUIBasedOnMode() {
            const formModeLabel = document.getElementById('formModeLabel'); 

            if (isRawMode) {
                if (progressContainer) progressContainer.style.display = 'none';
                if (multiStepButtons) multiStepButtons.style.display = 'none';
                if (rawSubmitButton) rawSubmitButton.style.display = 'block';
                
                formSections.forEach(section => { 
                    section.classList.toggle('active', section.id === 'section1');
                });
                
                if (rawModeToggle) rawModeToggle.checked = true;
                if (formModeLabel) formModeLabel.textContent = 'Raw Submit'; 
            } else {
                if (progressContainer) progressContainer.style.display = 'block';
                if (multiStepButtons) multiStepButtons.style.display = 'flex';
                if (rawSubmitButton) rawSubmitButton.style.display = 'none';
                
                showStep(currentStep); 
                
                if (rawModeToggle) rawModeToggle.checked = false;
                if (formModeLabel) formModeLabel.textContent = 'Full Submit'; 
            }
             if (previewContainer) previewContainer.style.display = 'block';
        }

        if (rawModeToggle) {
            rawModeToggle.addEventListener('change', function() {
                isRawMode = this.checked;
                localStorage.setItem('formMode', isRawMode ? 'raw' : 'full');
                updateUIBasedOnMode();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchData('/get-countries', 'country', 'Select Country');
            const storedFormMode = localStorage.getItem('formMode');
            if (storedFormMode === 'raw') {
                isRawMode = true;
            } else if (storedFormMode === 'full') {
                isRawMode = false;
            } else {
                // No mode stored, default to full submit mode
                isRawMode = false;
                localStorage.setItem('formMode', 'full'); // Optionally store the default for next time
            }
            updateUIBasedOnMode(); 
            setupFormSubmission();

            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('input', function() {
                    if (!isRawMode) {
                        const previewElement = document.getElementById(`preview-${this.id}`);
                        if (previewElement) {
                            previewElement.textContent = this.value || 'Not filled';
                        }
                        if (this.name.includes('[]') && typeof updateUniversityPreview === 'function') {
                            updateUniversityPreview();
                        }
                    }
                });
            });

            const academicInputDisplay = document.getElementById('academic');
            if (academicInputDisplay) {
                academicInputDisplay.addEventListener('click', function() {
                    const dropdownList = document.getElementById('dropdownList');
                    if (dropdownList) {
                        dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
                    }
                });
            }
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('dropdownList');
                const input = document.getElementById('academic');
                if (dropdown && input && event.target !== input && !dropdown.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });

            const sourceReferralSelect = document.getElementById('source');
            if (sourceReferralSelect) {
                const previewSourceReferral = document.getElementById('preview-source');
                sourceReferralSelect.addEventListener('change', function() {
                    if (previewSourceReferral && !isRawMode) previewSourceReferral.textContent = this.options[this.selectedIndex]?.text || this.value || 'Not filled';
                });
                if (previewSourceReferral && !isRawMode && sourceReferralSelect.value) { 
                     previewSourceReferral.textContent = sourceReferralSelect.options[sourceReferralSelect.selectedIndex]?.text || sourceReferralSelect.value || 'Not filled';
                }
            }

            const createdByDropdown = document.getElementById('created_by');
            const previewSourceUser = document.getElementById('preview-sources');
            if (createdByDropdown && previewSourceUser) {
                createdByDropdown.addEventListener('change', function() {
                    if (!isRawMode) previewSourceUser.textContent = this.options[this.selectedIndex]?.text || 'Not filled';
                });
                if (!isRawMode && createdByDropdown.value) { 
                     previewSourceUser.textContent = createdByDropdown.options[createdByDropdown.selectedIndex]?.text || 'Not filled';
                }
            }
        });

        async function sendFormData(formData) {
            const submitButton = isRawMode ? rawSubmitButton : document.querySelector('.next[type="submit"]');
            let originalButtonText = 'Submit';
            if (submitButton) {
                originalButtonText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';
            }

            if (!isRawMode) {
                updateSelectedDocumentsValue();
                const academicValue = document.getElementById('academicValue') ? document.getElementById('academicValue').value : '';
                formData.set('academic', academicValue || 'N/A');
            } else {
                formData.set('academic', 'N/A');
            }

            try {
                const response = await fetch(document.getElementById('studentForm').action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const responseText = await response.text();
                let result;
                try {
                    if (responseText.trim().startsWith('{') || responseText.trim().startsWith('[')) {
                        result = JSON.parse(responseText);
                    } else {
                        if (response.ok) {
                            result = {
                                success: true,
                                message: 'Form submitted successfully (non-JSON response).'
                            };
                        } else {
                            throw new Error(`Server returned non-JSON error: ${responseText.substring(0, 200)}...`);
                        }
                    }
                } catch (e) {
                    console.error('Response parsing error:', e);
                    console.log('Raw server response:', responseText);
                    throw new Error('Unable to process server response.');
                }

                if (!response.ok) {
                    throw new Error(result?.message || `Server error (${response.status}): ${response.statusText}`);
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.message || 'Your application has been submitted successfully!',
                    timer: 2500,
                    showConfirmButton: false
                });

                document.getElementById('studentForm').reset();
                if (window.selectedDocsMap) window.selectedDocsMap.clear();
                if (typeof updateDocumentLists === 'function') updateDocumentLists([]);
                if (typeof updateSelectedDocumentsValue === 'function') updateSelectedDocumentsValue();
                if (typeof resetDropdowns === 'function') resetDropdowns('country');

                if (!isRawMode) {
                    currentStep = 1;
                    showStep(currentStep);
                }
            } catch (error) {
                console.error('Form submission error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: `Application submission failed: ${error.message}. Please check your information and try again.`,
                });
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            }
        }

        function showStep(step) {
            if (isRawMode) return; 
            document.querySelectorAll('.form-section').forEach(section => section.classList.remove('active'));
            const currentSection = document.getElementById(`section${step}`);
            if (currentSection) {
                currentSection.classList.add('active');
            } else {
                console.error(`Form section ${step} not found!`);
                return;
            }

            const prevButton = document.querySelector('.prev');
            if (prevButton) {
                prevButton.style.display = step === 1 ? 'none' : 'inline-block';
            }

            const nextButton = document.querySelector('.next');
            if (!nextButton) return;

            if (step === totalSteps) {
                nextButton.textContent = 'Submit';
                nextButton.setAttribute('type', 'submit');
                nextButton.onclick = null; 
            } else {
                nextButton.textContent = 'Next';
                nextButton.setAttribute('type', 'button');
                nextButton.onclick = nextStep;
            }
            updateProgressBar(step);
            if (!isRawMode && previewSections[step - 1]) {
                scrollToPreview(previewSections[step - 1]);
            }
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
            return false; 
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
            return false; 
        }

        function updateProgressBar(step) {
            document.querySelectorAll('.progress-step').forEach((el, index) => {
                if (el) {
                    if (index + 1 < step) {
                        el.classList.add('completed');
                        el.classList.remove('active');
                    } else if (index + 1 === step) {
                        el.classList.add('active');
                        el.classList.remove('completed');
                    } else {
                        el.classList.remove('active', 'completed');
                    }
                }
            });
        }

        function scrollToPreview(sectionId) {
            if (isRawMode) return;
            const target = document.getElementById(sectionId);
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            }
        }

        function updateSelectedDocumentsValue() {
            const selectedItems = document.querySelectorAll('#selectedDocuments .document-item');
            const selectedDocs = Array.from(selectedItems).map(item => item.dataset.document);
            const academicValueString = selectedDocs.length > 0 ? selectedDocs.join(',') : '';

            const hiddenInput = document.getElementById('academicValue');
            if (hiddenInput) hiddenInput.value = academicValueString;

            const visibleInput = document.getElementById('academic');
            if (visibleInput) visibleInput.value = selectedDocs.length > 0 ? `${selectedDocs.length} document(s) selected` : 'Select Documents';


            if (!isRawMode) {
                const previewElement = document.getElementById('preview-documents');
                if (previewElement) previewElement.textContent = academicValueString || 'Not filled';
            }
        }

        function setupFormSubmission() {}

        function resetDropdowns(startFrom) {
            const dropdowns = {
                'country': 'Select Country',
                'location': 'Select Location',
                'university1': 'Select University',
                'course1': 'Select Course',
                'intake1': 'Select Intake'
            };
            const previewIds = {
                'country': 'preview-country',
                'location': 'preview-location'
            }
            let shouldReset = (startFrom === 'country');

            for (const [id, text] of Object.entries(dropdowns)) {
                if (id === startFrom) shouldReset = true;
                if (shouldReset) {
                    const element = document.getElementById(id);
                    if (element) element.innerHTML = `<option value="" disabled selected>${text}</option>`;
                    const previewId = previewIds[id];
                    if (previewId) {
                        const previewElement = document.getElementById(previewId);
                        if (previewElement && !isRawMode) previewElement.textContent = 'Not filled';
                    }
                }
            }

            if (shouldReset && (startFrom === 'location' || startFrom === 'country')) {
                const selectedDocsEl = document.getElementById('selectedDocuments');
                const unselectedDocsEl = document.getElementById('unselectedDocuments');
                const academicInputEl = document.getElementById('academic');
                const academicValueEl = document.getElementById('academicValue');
                const dropdownListEl = document.getElementById('dropdownList');
                if (selectedDocsEl) selectedDocsEl.innerHTML = '';
                if (unselectedDocsEl) unselectedDocsEl.innerHTML = '';
                if (academicInputEl) academicInputEl.value = 'Select Documents';
                if (academicValueEl) academicValueEl.value = '';
                if (dropdownListEl) dropdownListEl.innerHTML = '';
                const docPreview = document.getElementById('preview-documents');
                if (docPreview && !isRawMode) docPreview.textContent = 'Not filled';
            }
            if (shouldReset && (startFrom === 'university1' || startFrom === 'location' || startFrom === 'country')) {
                if (typeof updateUniversityPreview === 'function') updateUniversityPreview();
            }
        }

        async function fetchData(url, dropdownId, defaultText) {
            const select = document.getElementById(dropdownId);
            if (!select) return;
            select.innerHTML = `<option value="" disabled selected>Loading...</option>`;
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    updateDropdown(dropdownId, result.data, defaultText);
                } else {
                    if (result.data === null || (Array.isArray(result.data) && result.data.length === 0)) {
                        updateDropdown(dropdownId, [], defaultText); 
                    } else {
                        console.warn(`Received non-success or malformed data for ${dropdownId}:`, result);
                        updateDropdown(dropdownId, [], defaultText); 
                    }
                }
            } catch (error) {
                handleApiError(error, dropdownId);
            }
        }

        function updateDropdown(elementId, data, defaultText = 'Select Option') {
            const select = document.getElementById(elementId);
            if (!select) return;
            select.innerHTML = `<option value="" disabled selected>${defaultText}</option>`;
            if (data && Array.isArray(data)) {
                data.forEach(item => {
                    const option = document.createElement('option');
                    const value = typeof item === 'object' && item !== null ? (item.id || item.name || item.value) : item;
                    const text = typeof item === 'object' && item !== null ? (item.name || item.value || item.id) : item;
                    option.value = value;
                    option.textContent = text;
                    select.appendChild(option);
                });
            }
        }

        function handleApiError(error, dropdownId) {
            console.error(`API Error for ${dropdownId}:`, error);
            const select = document.getElementById(dropdownId);
            if (select) select.innerHTML = `<option value="" disabled selected>Error loading</option>`;
        }

        function handleCountryChange() {
            const countryDropdown = document.getElementById('country');
            const country = countryDropdown ? countryDropdown.value : '';
            resetDropdowns('location'); 
            const previewEl = document.getElementById('preview-country');
             if (previewEl && !isRawMode) { 
                previewEl.textContent = country ? countryDropdown.options[countryDropdown.selectedIndex].text : 'Not filled';
            }
            if (country) fetchData(`/get-locations-by-country?country=${encodeURIComponent(country)}`, 'location', 'Select Location');
        }

        function handleLocationChange() {
            const locationDropdown = document.getElementById('location');
            const location = locationDropdown ? locationDropdown.value : '';
            resetDropdowns('university1'); 
            const previewEl = document.getElementById('preview-location');
            if (previewEl && !isRawMode) { 
                 previewEl.textContent = location ? locationDropdown.options[locationDropdown.selectedIndex].text : 'Not filled';
            }
            if (location) fetchData(`/get-universities-by-location?location=${encodeURIComponent(location)}`, 'university1', 'Select University');
        }

        function handleUniversityChange() {
            const university = document.getElementById('university1')?.value;
            const location = document.getElementById('location')?.value; 
            resetDropdowns('course1');
            if (typeof updateUniversityPreview === 'function') updateUniversityPreview(); 
            if (university && location) {
                fetchData(`/get-courses-by-university?university=${encodeURIComponent(university)}&location=${encodeURIComponent(location)}`, 'course1', 'Select Course');
                fetchRequiredDocuments(university); 
            } else {
                if (typeof updateDocumentLists === 'function') updateDocumentLists([]);
                if (typeof setupDropdownList === 'function') setupDropdownList([]);
                if (typeof updateSelectedDocumentsValue === 'function') updateSelectedDocumentsValue();
            }
        }

        function handleCourseChange() {
            const course = document.getElementById('course1')?.value;
            const university = document.getElementById('university1')?.value;
            const location = document.getElementById('location')?.value; 
            resetDropdowns('intake1');
            if (typeof updateUniversityPreview === 'function') updateUniversityPreview(); 
            if (course && university && location) {
                fetchData(`/get-intakes-by-course?course=${encodeURIComponent(course)}&university=${encodeURIComponent(university)}&location=${encodeURIComponent(location)}`, 'intake1', 'Select Intake');
            }
        }

        async function fetchRequiredDocuments(university) {
            if (typeof updateDocumentLists === 'function') updateDocumentLists([]);
            if (typeof setupDropdownList === 'function') setupDropdownList([]);
            if (typeof updateSelectedDocumentsValue === 'function') updateSelectedDocumentsValue();

            const country = document.getElementById('country')?.value;
            if (!university || !country) {
                return; 
            }

            try {
                const response = await fetch(`/get-required-documents?university=${encodeURIComponent(university)}&country=${encodeURIComponent(country)}`);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                if (result.status === 'success' && result.data && Array.isArray(result.data)) {
                    if (typeof updateDocumentLists === 'function') updateDocumentLists(result.data);
                    if (typeof setupDropdownList === 'function') setupDropdownList(result.data);
                } else {
                    if (typeof updateDocumentLists === 'function') updateDocumentLists([]); 
                    if (typeof setupDropdownList === 'function') setupDropdownList([]);
                }
            } catch (error) {
                console.error('Error fetching documents:', error);
                const unselectedContainer = document.getElementById('unselectedDocuments');
                if (unselectedContainer) unselectedContainer.innerHTML = `<div class="document-error">Could not load docs: ${error.message}</div>`;
                const academicInputEl = document.getElementById('academic');
                const academicValueEl = document.getElementById('academicValue');
                const dropdownListEl = document.getElementById('dropdownList');
                if(academicInputEl) academicInputEl.value = 'Select Documents';
                if(academicValueEl) academicValueEl.value = '';
                if(dropdownListEl) dropdownListEl.innerHTML = '';
            }
        }
        
        function setupDropdownList(documents) {
            const dropdownList = document.getElementById('dropdownList');
            if (!dropdownList) return;
            dropdownList.innerHTML = ''; 

            if (documents && Array.isArray(documents) && documents.length > 0) {
                const currentlySelected = new Set((document.getElementById('academicValue')?.value || '').split(',').filter(Boolean));
                documents.forEach(doc => {
                    if (!doc) return; 
                    const item = document.createElement('div');
                    item.className = 'dropdown-item flex items-center p-2 hover:bg-gray-100';
                    
                    const checkboxId = `dropdown-${doc.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase()}`;
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = checkboxId;
                    checkbox.className = 'document-dropdown-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-2';
                    checkbox.value = doc;
                    checkbox.checked = currentlySelected.has(doc); 

                    checkbox.addEventListener('change', function() {
                        handleDocumentSelectionChange(this.value, this.checked);
                    });

                    const label = document.createElement('label');
                    label.htmlFor = checkbox.id; 
                    label.textContent = doc;
                    label.className = 'flex-grow cursor-pointer text-sm text-gray-700';

                    item.appendChild(checkbox);
                    item.appendChild(label);
                    dropdownList.appendChild(item);
                });
            } else {
                dropdownList.innerHTML = '<div class="dropdown-message p-2 text-sm text-gray-500 italic">No documents specified</div>';
            }
        }

        function handleDocumentSelectionChange(docName, isChecked) {
            const selectedContainer = document.getElementById('selectedDocuments');
            const unselectedContainer = document.getElementById('unselectedDocuments');
            if(!selectedContainer || !unselectedContainer) return;

            let docElement = selectedContainer.querySelector(`.document-item[data-document="${CSS.escape(docName)}"]`) || 
                             unselectedContainer.querySelector(`.document-item[data-document="${CSS.escape(docName)}"]`);

            if (!docElement) { 
                docElement = createDocumentElement(docName);
                if (isChecked) {
                    selectedContainer.appendChild(docElement);
                } else {
                    unselectedContainer.appendChild(docElement);
                }
            } else { 
                if (isChecked) {
                    if (docElement.parentElement !== selectedContainer) selectedContainer.appendChild(docElement);
                } else {
                    if (docElement.parentElement !== unselectedContainer) unselectedContainer.appendChild(docElement);
                }
            }
            const itemCheckbox = docElement.querySelector('.document-checkbox');
            if (itemCheckbox) itemCheckbox.checked = isChecked;

            updateSelectedDocumentsValue();
        }

        function updateDocumentLists(documents) {
            const selectedContainer = document.getElementById('selectedDocuments');
            const unselectedContainer = document.getElementById('unselectedDocuments');
            if (!selectedContainer || !unselectedContainer) return;

            const currentlySelected = new Set((document.getElementById('academicValue')?.value || '').split(',').filter(Boolean));

            selectedContainer.innerHTML = ''; 
            unselectedContainer.innerHTML = ''; 

            if (documents && Array.isArray(documents) && documents.length > 0) {
                documents.forEach(doc => {
                    if (!doc) return; 
                    const docElement = createDocumentElement(doc);
                    const itemCheckbox = docElement.querySelector('.document-checkbox');

                    if (currentlySelected.has(doc)) {
                        if(itemCheckbox) itemCheckbox.checked = true;
                        selectedContainer.appendChild(docElement);
                    } else {
                        if(itemCheckbox) itemCheckbox.checked = false;
                        unselectedContainer.appendChild(docElement);
                    }
                });
            } else {
                unselectedContainer.innerHTML = '<div class="document-message p-2 text-sm text-gray-500 italic">No document requirements specified</div>';
            }
            updateSelectedDocumentsValue(); 
        }

        function createDocumentElement(docName) {
            const div = document.createElement('div');
            div.className = 'document-item flex items-center p-2 border rounded mb-2 bg-white hover:bg-gray-50';
            div.dataset.document = docName; 

            const checkboxId = `doc-${docName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase()}`;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = checkboxId;
            checkbox.className = 'document-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-3 flex-shrink-0';
            checkbox.value = docName;

            const label = document.createElement('label');
            label.htmlFor = checkboxId; 
            label.textContent = docName;
            label.className = 'flex-grow cursor-pointer text-sm text-gray-800';
            
            div.addEventListener('click', function(e) {
                if (e.target !== checkbox) { 
                    checkbox.checked = !checkbox.checked;
                    const changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);
                }
            });

            checkbox.addEventListener('change', function() {
                const dropdownCheckboxId = `dropdown-${docName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase()}`;
                const dropdownCheckbox = document.getElementById(dropdownCheckboxId);
                if (dropdownCheckbox) dropdownCheckbox.checked = this.checked;

                const targetContainer = this.checked ? document.getElementById('selectedDocuments') : document.getElementById('unselectedDocuments');
                if (targetContainer && this.parentElement.parentElement !== targetContainer) {
                     targetContainer.appendChild(this.parentElement); 
                }
                updateSelectedDocumentsValue();
            });

            div.appendChild(checkbox);
            div.appendChild(label);
            return div;
        }

        function updateUniversityPreview() {
            if (isRawMode) return; 
            const previewContainer = document.getElementById('preview-universities');
            if (!previewContainer) return;

            previewContainer.innerHTML = ''; 

            for (let i = 1; i <= formCounter; i++) {
                const universityEl = document.getElementById(`university${i}`);
                const courseEl = document.getElementById(`course${i}`);
                const intakeEl = document.getElementById(`intake${i}`);

                const university = universityEl && universityEl.value ? (universityEl.options[universityEl.selectedIndex]?.text || universityEl.value) : 'Not filled';
                const course = courseEl && courseEl.value ? (courseEl.options[courseEl.selectedIndex]?.text || courseEl.value) : 'Not filled';
                const intake = intakeEl && intakeEl.value ? (intakeEl.options[intakeEl.selectedIndex]?.text || intakeEl.value) : 'Not filled';

                if (university !== 'Not filled' || course !== 'Not filled' || intake !== 'Not filled') {
                    const entryDiv = document.createElement('div');
                    entryDiv.className = 'university-entry border p-3 rounded bg-gray-50 mb-2 text-sm';
                    entryDiv.innerHTML = 
                        `<p class="mb-1"><strong class="text-gray-700">University ${i}:</strong> <span class="text-gray-600">${university}</span></p>` +
                        `<p class="mb-1"><strong class="text-gray-700">Course:</strong> <span class="text-gray-600">${course}</span></p>` +
                        `<p><strong class="text-gray-700">Intake:</strong> <span class="text-gray-600">${intake}</span></p>`;
                    previewContainer.appendChild(entryDiv);
                }
            }

            if (previewContainer.innerHTML === '') {
                previewContainer.innerHTML = '<p class="text-sm text-gray-500 italic">No university preferences added.</p>';
            }
        }

        function addFields() {}
        function removeFields() {}

        async function generatePdfFromCanvas() { 
            const downloadButton = document.getElementById('download-pdf'); 
            const originalButtonText = downloadButton.textContent;
            downloadButton.disabled = true;
            downloadButton.textContent = 'Generating PDF...';

            const elementToCapture = document.querySelector('.form-preview');
            const previewContainer = document.querySelector('.preview-container'); 

            if (!elementToCapture || !previewContainer) {
                console.error("Preview element or container not found.");
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not find preview elements.'
                });
                downloadButton.disabled = false;
                downloadButton.textContent = originalButtonText;
                return;
            }

            if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
                console.error("jsPDF library not found. Make sure html2pdf.bundle.js is loaded.");
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PDF Library (jsPDF) not found.'
                });
                downloadButton.disabled = false;
                downloadButton.textContent = originalButtonText;
                return;
            }
            const {
                jsPDF
            } = window.jspdf; 

            const originalPreviewContainerStyle = previewContainer.style.cssText;
            const originalElementToCaptureStyle = elementToCapture.style.cssText;

            previewContainer.style.height = 'auto';
            previewContainer.style.maxHeight = 'none';
            previewContainer.style.overflowY = 'visible';
            elementToCapture.style.height = 'auto';
            elementToCapture.style.maxHeight = 'none';

            await new Promise(resolve => setTimeout(resolve, 150)); 

            const canvasOptions = {
                scale: 2, 
                useCORS: true,
                logging: false, 
                backgroundColor: '#ffffff', 
            };

            try {
                const canvas = await html2canvas(elementToCapture, canvasOptions);
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;

                const pdf = new jsPDF({
                    orientation: 'p', 
                    unit: 'pt',
                    format: 'letter' 
                });

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();

                const margin = 40; 
                const usableWidth = pdfWidth - (2 * margin);
                const usableHeight = pdfHeight - (2 * margin);

                const widthRatio = usableWidth / imgWidth;
                const heightRatio = usableHeight / imgHeight;
                let scaleRatio = Math.min(widthRatio, heightRatio);

                let finalImgWidth = imgWidth * scaleRatio;
                let finalImgHeight = imgHeight * scaleRatio;

                let yPos = margin;
                let remainingHeight = imgHeight; 
                const pageHeightInCanvasPixels = usableHeight / scaleRatio;

                while (remainingHeight > 0) {
                    if (yPos !== margin) { 
                        pdf.addPage();
                        yPos = margin; 
                    }

                    let sliceHeight = Math.min(pageHeightInCanvasPixels, remainingHeight);

                    const sliceCanvas = document.createElement('canvas');
                    sliceCanvas.width = imgWidth; 
                    sliceCanvas.height = sliceHeight; 
                    const sliceCtx = sliceCanvas.getContext('2d');

                    sliceCtx.drawImage(canvas, 0, (imgHeight - remainingHeight), imgWidth, sliceHeight, 0, 0, imgWidth, sliceHeight);
                    
                    const sliceImgData = sliceCanvas.toDataURL('image/png');
                    const slicePdfHeight = sliceHeight * scaleRatio;
                    const slicePdfWidth = imgWidth * scaleRatio; 

                    pdf.addImage(sliceImgData, 'PNG', margin, yPos, slicePdfWidth, slicePdfHeight);
                    
                    remainingHeight -= sliceHeight;
                }

                let studentName = (document.getElementById('preview-name')?.textContent || 'Student')
                    .replace(/[^a-z0-9_-\s]/gi, '')
                    .trim() || 'Lead_Preview';
                const fileName = `${studentName}_Lead_Form_Preview.pdf`; 

                pdf.save(fileName);

            } catch (error) {
                console.error('Error generating PDF from canvas:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'PDF Generation Failed',
                    text: `An error occurred: ${error}. Check console for details.`
                });
            } finally {
                previewContainer.style.cssText = originalPreviewContainerStyle;
                elementToCapture.style.cssText = originalElementToCaptureStyle;
                downloadButton.disabled = false;
                downloadButton.textContent = originalButtonText; 
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const downloadButton = document.getElementById('download-pdf'); 
            if (downloadButton) {
                downloadButton.addEventListener('click', async function(event) { 
                    event.preventDefault();
                    await generatePdfFromCanvas(); 
                });
            }
        });

        document.getElementById('studentForm').addEventListener('submit', function(event) {
            event.preventDefault();
            try {
                const formData = new FormData();
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!token) throw new Error('Security token missing.');
                formData.append('_token', token);

                const getFieldValue = (id) => {
                    const element = document.getElementById(id);
                    return element ? element.value.trim() : '';
                };

                const createdById = getFieldValue('created_by');
                formData.append('created_by', createdById || '');

                if (isRawMode) {
                    formData.append('sources', '1');
                } else {
                    formData.append('sources', '0');
                }

                if (isRawMode) {
                    formData.append('submission_type', 'raw');

                    const nameValue = getFieldValue('name');
                    const phoneValue = getFieldValue('phone');

                    if (!nameValue) {
                        throw new Error('Name is required for raw submission.');
                    }
                    if (!phoneValue) {
                        throw new Error('Phone Number is required for raw submission.');
                    }

                    formData.append('name', nameValue);
                    formData.append('phone', phoneValue);
                    formData.append('email', getFieldValue('email') || 'N/A');
                    formData.append('locations', getFieldValue('locations') || 'N/A');
                    formData.append('link', getFieldValue('link') || 'N/A');

                    const fieldsToBeN_A_InRawMode = [
                        'lastqualification', 'courselevel', 'passed', 'gpa',
                        'englishTest', 'higher', 'less', 'score', 'englishscore', 'englishtheory', 'otherScore',
                        'source', 'otherDetails', 'partnerDetails',
                        'country', 'location',
                        'offerss', 'status'
                    ];

                    fieldsToBeN_A_InRawMode.forEach(field => {
                        formData.append(field, 'N/A');
                    });

                    formData.append('university[]', 'N/A');
                    formData.append('course[]', 'N/A');
                    formData.append('intake[]', 'N/A');

                } else {
                    formData.append('submission_type', 'full');
                    updateSelectedDocumentsValue();

                    const requiredFields = ['name', 'phone'];
                    for (const field of requiredFields) {
                        const value = getFieldValue(field);
                        if (!value) {
                            throw new Error(`${field.charAt(0).toUpperCase() + field.slice(1)} is required`);
                        }
                        formData.append(field, value);
                    }
                    formData.append('email', getFieldValue('email') || 'N/A');

                    const optionalFields = [
                        'locations', 'lastqualification', 'courselevel',
                        'passed', 'gpa', 'englishTest', 'higher', 'less',
                        'score', 'englishscore', 'englishtheory', 'otherScore', 'country',
                        'location', 'source', 'otherDetails', 'partnerDetails', 'link', 'status'
                    ];
                    for (const field of optionalFields) {
                        formData.append(field, getFieldValue(field) || 'N/A');
                    }
                    formData.append('offerss', 'N/A');

                    const universities = [];
                    const courses = [];
                    const intakes = [];
                    for (let i = 1; i <= formCounter; i++) {
                        const uni = getFieldValue(`university${i}`);
                        const course = getFieldValue(`course${i}`);
                        const intake = getFieldValue(`intake${i}`);
                        if (uni || course || intake) {
                            universities.push(uni || 'N/A');
                            courses.push(course || 'N/A');
                            intakes.push(intake || 'N/A');
                        }
                    }
                    if (universities.length === 0) {
                        formData.append('university[]', 'N/A');
                        formData.append('course[]', 'N/A');
                        formData.append('intake[]', 'N/A');
                    } else {
                        universities.forEach(uni => formData.append('university[]', uni));
                        courses.forEach(crs => formData.append('course[]', crs));
                        intakes.forEach(intk => formData.append('intake[]', intk));
                    }
                }

                sendFormData(formData);

            } catch (error) {
                console.error('Form preparation error:', error);
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Error',
                    text: `Error: ${error.message}. Please check your information.`,
                });
                const submitBtn = isRawMode ? rawSubmitButton : document.querySelector('.next[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = isRawMode ? 'Submit Raw Data' : 'Submit';
                }
            }
        });

    </script>

</body>
@endsection