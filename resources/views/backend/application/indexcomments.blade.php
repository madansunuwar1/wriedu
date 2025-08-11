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
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Comment Data</div>
                </div>
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

        <div class="card mt-4">
            <div class="border-bottom title-part-padding">
                <h4 class="card-title mb-0">Add Dynamic Comment</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="commentForm" action="{{ route('backend.application.storecomments') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="applications">Comment Type</label>
                            <input type="text" class="form-control" id="applications" name="applications" 
                                   placeholder="Enter comment type" required>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please provide a comment type.</div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-message-plus me-1"></i> Add Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-4 mb-4 border rounded-1">
            <table class="table text-nowrap mb-0 align-middle" id="commentTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th><h6 class="fs-4 fw-semibold mb-0">Comment</h6></th>
                        <th><h6 class="fs-4 fw-semibold mb-0">Actions</h6></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commentAdds as $commentadd)
                    <tr>
                        <td>{{ $commentadd->applications }}</td>
                        <td>
                            <div class="dropdown dropstart">
                                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical fs-6"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('comment.edit',  $commentadd->id) }}">
                                            <i class="fs-4 ti ti-edit"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('comment.destroy',  $commentadd->id) }}" method="POST" onsubmit="return confirmDelete()">
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
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this comment?');
    }

    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var form = document.getElementById('commentForm');
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