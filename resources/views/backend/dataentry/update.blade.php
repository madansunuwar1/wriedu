@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <div class="card">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Edit University Data</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('backend.dataentry.update', $data_entries->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <!-- University and Location section -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newUniversity">University</label>
                        <input type="text" class="form-control" id="newUniversity" name="newUniversity" 
                               value="{{ old('newUniversity', $data_entries->newUniversity) }}" required>
                        <div class="invalid-feedback">Please provide university name.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newLocation">Location</label>
                        <input type="text" class="form-control" id="newLocation" name="newLocation" 
                               value="{{ old('newLocation', $data_entries->newLocation) }}" required>
                        <div class="invalid-feedback">Please provide location.</div>
                    </div>
                </div>

                <!-- Course and Intake section -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newCourse">Course</label>
                        <input type="text" class="form-control" id="newCourse" name="newCourse" 
                               value="{{ old('newCourse', $data_entries->newCourse) }}" required>
                        <div class="invalid-feedback">Please provide course name.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newIntake">Intake</label>
                        <input type="text" class="form-control" id="newIntake" name="newIntake" 
                               value="{{ old('newIntake', $data_entries->newIntake) }}" list="intakeList" required>
                        <datalist id="intakeList">
                            @php
                                $months = ["January", "February", "March", "April", "May", "June", 
                                          "July", "August", "September", "October", "November", "December"];
                            @endphp
                            @foreach($months as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </datalist>
                        <div class="invalid-feedback">Please provide intake period.</div>
                    </div>
                </div>

                <!-- Scholarship and Tuition section -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newScholarship">Scholarship</label>
                        <input type="text" class="form-control" id="newScholarship" name="newScholarship" 
                               value="{{ old('newScholarship', $data_entries->newScholarship) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newAmount">Tuition Fees</label>
                        <input type="text" class="form-control" id="newAmount" name="newAmount" 
                               value="{{ old('newAmount', $data_entries->newAmount) }}">
                    </div>
                </div>

                <!-- UG Requirements section -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Undergraduate Requirements</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newIelts">IELTS Requirement</label>
                                <input type="text" class="form-control" id="newIelts" name="newIelts" 
                                       value="{{ old('newIelts', $data_entries->newIelts) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newpte">PTE Requirement</label>
                                <input type="text" class="form-control" id="newpte" name="newpte" 
                                       value="{{ old('newpte', $data_entries->newpte) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newug">Gap Accepted</label>
                                <input type="text" class="form-control" id="newug" name="newug" 
                                       value="{{ old('newug', $data_entries->newug) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newgpaug">GPA/Percentage</label>
                                <input type="text" class="form-control" id="newgpaug" name="newgpaug" 
                                       value="{{ old('newgpaug', $data_entries->newgpaug) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PG Requirements section -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Postgraduate Requirements</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newPgIelts">IELTS Requirement</label>
                                <input type="text" class="form-control" id="newPgIelts" name="newPgIelts" 
                                       value="{{ old('newPgIelts', $data_entries->newPgIelts) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newPgPte">PTE Requirement</label>
                                <input type="text" class="form-control" id="newPgPte" name="newPgPte" 
                                       value="{{ old('newPgPte', $data_entries->newPgPte) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newpg">Gap Accepted</label>
                                <input type="text" class="form-control" id="newpg" name="newpg" 
                                       value="{{ old('newpg', $data_entries->newpg) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="newgpapg">GPA/Percentage</label>
                                <input type="text" class="form-control" id="newgpapg" name="newgpapg" 
                                       value="{{ old('newgpapg', $data_entries->newgpapg) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- English Waiver and Country section -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="newtest">English Waiver Requirement</label>
                        <input type="text" class="form-control" id="newtest" name="newtest" 
                               value="{{ old('newtest', $data_entries->newtest) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="country">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="" disabled>Select Country</option>
                            <option value="USA" {{ old('country', $data_entries->country) == 'USA' ? 'selected' : '' }}>USA</option>
                            <option value="UK" {{ old('country', $data_entries->country) == 'UK' ? 'selected' : '' }}>UK</option>
                            <option value="Australia" {{ old('country', $data_entries->country) == 'Australia' ? 'selected' : '' }}>Australia</option>
                            <option value="Canada" {{ old('country', $data_entries->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                            <option value="New Zealand" {{ old('country', $data_entries->country) == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
                        </select>
                        <div class="invalid-feedback">Please select country.</div>
                    </div>
                </div>

                <!-- Documents and Level section -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="requireddocuments">Required Documents</label>
                        <textarea class="form-control" id="requireddocuments" name="requireddocuments" 
                                  rows="3" required>{{ old('requireddocuments', $data_entries->requireddocuments) }}</textarea>
                        <div class="invalid-feedback">Please list required documents.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="level">Level</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="" disabled>Select Level</option>
                            <option value="undergraduate" {{ old('level', $data_entries->level) == 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                            <option value="postgraduate" {{ old('level', $data_entries->level) == 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                            <option value="both" {{ old('level', $data_entries->level) == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                        <div class="invalid-feedback">Please select level.</div>
                    </div>
                </div>

                <!-- Submit button -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy me-1"></i> Update University Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .title-part-padding {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.25rem;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            padding: 10px 25px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
        }
        .card-body .card {
            background-color: #f8f9fa;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .card-body .card-title {
            color: #4CAF50;
            font-size: 1.1rem;
        }
    </style>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection