@extends('layouts.admin')

@include('backend.script.session')

@include('backend.script.alert')

@section('content')
    <style>
        body {
            background-color: white; /* Bootstrap success green */
        }
        .card {
            background-color: white;
        }
       
    </style>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4> Update Document</h4>
                    </div>
                    <div class="card-body">
                        <!-- Laravel Form -->
                        <form action="{{ route('backend.document.update', $documents->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        
                            <div class="mb-3">
                                <label for="document" class="form-label">Enter Document</label>
                                <input 
                                    type="text" 
                                    name="document" 
                                    id="document" 
                                    class="form-control" 
                                    placeholder="Enter your document"  value="{{ old('document', $documents->document) }}"
                                    required
                                >
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Update Document</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
