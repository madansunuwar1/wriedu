@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')
@include('backend.script.media')

@section('content')
<script src="{{ asset('js/session-manager.js') }}"></script>

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-9 col-xl-9">
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Document Management</div>
                </div>
            </div>
        </div>

        @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-custom-popup', // Custom class for the popup
                    confirmButton: 'swal-custom-ok-button' // Custom class for the OK button
                }
            });
        </script>
        @endif

        <div id=" add-document-modal" class="flex items-center justify-center">
            <div class="bg-white rounded-lg w-1/3 p-6 shadow-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">Add New Document</h2>
                </div>

                
                <div class="card p-2">
        <form action="{{ route('backend.document.store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="document" class="form-label">Enter Document</label>
                        <input 
                            type="text" 
                            name="document" 
                            id="document" 
                            class="form-control" 
                            placeholder="Enter your document" 
                            required
                        >
                    </div>

                    <div class="mb-3">
                                <label for="country" class="form-label">Select Country</label>
                                <select name="country" id="country" class="form-control" required>
                                    <option value="" disabled selected>Select a country</option>
                                    @foreach($data_entries as $data_entry)
                                        <option value="{{ $data_entry->country }}">{{ $data_entry->country }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Select Document Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="" disabled selected>Select an Application</option>
                                    <option value="lead">Lead</option>
                                    <option value="application">Application</option>
                                </select>
                            </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
     

        <div class="table-responsive mb-4 border rounded-1">
            <table class="table text-nowrap mb-0 align-middle" id="documentTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Document</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Country</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Application</h6>
                        </th>
                        <th>
                            <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($documents as $document)
    <tr>
        <td>{{ $document->document }}</td>
        <td>{{ $document->country }}</td>
        <td>{{ $document->status }}</td>
        <td>
            <div class="dropdown dropstart">
                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical fs-6"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                        <form action="{{ route('document.destroy', $document->id) }}" method="POST" onsubmit="return confirmDelete()">
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
    const addButton = document.getElementById('add-document-btn');
    const modal = document.getElementById('add-document-modal');
    const closeModal = document.getElementById('close-modal');

    addButton.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });

    function confirmDelete() {
        return confirm('Are you sure you want to delete this document?');
    }
</script>

@include('backend.script.pagination')

@endsection
