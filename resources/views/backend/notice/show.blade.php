@extends('layouts.admin')
@section('content')

    <div class="container notice-container">
        <div class="card overflow-hidden shadow-sm">
            <!-- Notice Header with Image -->
            <div class="position-relative">
                @if($notice->image)
                <img src="{{ asset('storage/' . $notice->image) }}" class="notice-img" alt="{{ $notice->title }}">
                <span class="badge bg-light text-dark position-absolute bottom-0 end-0 m-3">
                    <i class="bi bi-clock"></i> {{ $notice->created_at->diffForHumans() }}
                </span>
                @endif
                
                @if(auth()->check())
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}" 
                     alt="Author" 
                     class="rounded-circle position-absolute bottom-0 start-0 mb-n3 ms-3 author-img"
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top" 
                     data-bs-title="{{ auth()->user()->name }}">
                @endif
            </div>

            <!-- Notice Body -->
            <div class="card-body p-4">
                <!-- Category and Title -->
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Announcement</span>
                <h1 class="fw-semibold mb-3">{{ $notice->title }}</h1>
                
                <!-- Meta Information -->
                <div class="d-flex align-items-center gap-4 text-muted mb-4">
                
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar"></i> {{ $notice->created_at->format('M d, Y') }}
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <i class="bi bi-clock-history"></i> 
                        {{ $notice->display_start_at->format('M d') }} - {{ $notice->display_end_at->format('M d, Y') }}
                    </div>
                </div>

                <!-- Notice Content -->
                <div class="notice-content mt-4">
                    {!! nl2br(e($notice->description)) !!}
                </div>

                <!-- Additional Content Sections -->
                @if($notice->additional_content)
                <div class="border-top mt-5 pt-5">
                    <h3 class="fw-semibold mb-3">Additional Details</h3>
                    <div class="notice-content">
                        {!! nl2br(e($notice->additional_content)) !!}
                    </div>
                </div>
                @endif

                <!-- Quote Section (if applicable) -->
                @if($notice->quote)
                <div class="border-top mt-5 pt-5">
                    <div class="quote-block">
                        <i class="bi bi-quote fs-4 text-primary"></i>
                        <span class="fs-5">{{ $notice->quote }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center py-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                
                <div class="d-flex gap-2">
                    @if(auth()->check() && auth()->user()->can('edit-notices'))
                    <a href="{{ route('backend.notice.edit', $notice->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                    @endif
                    
                    <button class="btn btn-light">
                        <i class="bi bi-share"></i>
                    </button>
                    <button class="btn btn-light">
                        <i class="bi bi-bookmark"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection