@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <div class="card">
        <div class="border-bottom title-part-padding">
            <h4 class="card-title mb-0">Edit Comment</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('backend.application.updatecommentts', $comment->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <!-- Application Type -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="application">Application Type</label>
                        <select class="form-select" id="application" name="application" required>
                            <option value="" disabled>Select type of application</option>
                            @foreach ($commentadds as $commentadd)
                                <option value="{{ $commentadd->applications }}"
                                    {{ old('application', $comment->application) == $commentadd->applications ? 'selected' : '' }}>
                                    {{ $commentadd->applications }}
                                </option>
                            @endforeach
                        </select>
                        @error('application')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Comment -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="5" required>{{ old('comment', $comment->comment) }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-device-floppy me-1"></i> Update Comment
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
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