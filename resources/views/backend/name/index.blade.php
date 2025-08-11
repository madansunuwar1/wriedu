@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<script src="{{ asset('js/session-manager.js') }}"></script>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-9 col-xl-9">
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Counselor Index</div>
                </div>
                <!-- <div class="col-md-3 col-xl-3 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <a href="{{ route('backend.name.create') }}" class="btn btn-success d-flex align-items-center">
                        <i class="ti ti-plus text-white me-1 fs-5"></i> Add Counselor
                    </a>
                </div> -->
            </div>
        </div>
        <div class="card mt-4">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Add Counselor</h4>
        </div>
        <div class="card-body">
            <form id="counselorForm" action="{{ route('backend.name.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label" for="name">Counselor Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter counselor name" required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please provide a counselor name.</div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-plus me-1"></i> Add Counselor
                    </button>
                </div>
            </form>
        </div>
    </div>

        @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
        @endif

        <div class="table-responsive mb-4 border rounded-1">
            <table class="table text-nowrap mb-0 align-middle" id="counselorTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Counselor Name</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($names as $name)
                    <tr>
                        <td>{{ $name->name }}</td>
                        <td>
                            <div class="dropdown dropstart">
                                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical fs-6"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('name.edit', $name->id) }}">
                                            <i class="fs-4 ti ti-edit"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('name.destroy', $name->id) }}" method="POST" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-3 text-danger">
                                                <i class="fs-4 ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Counselor Form -->
    
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this counselor?');
    }

    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var form = document.getElementById('counselorForm');
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }, false);
    })();
</script>
@endsection
