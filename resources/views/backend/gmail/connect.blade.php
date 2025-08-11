@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gmail Account Integration</h5>
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                
                <div class="card-body">
                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Connection Instructions --}}
                    <div class="mb-4">
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Connect your Gmail account to enable seamless email integration with our platform.
                        </p>
                    </div>

                    {{-- Connect Button --}}
                    <div class="text-center mb-4">
                        <a href="{{ route('google.redirect') }}" class="btn btn-primary btn-lg">
                            <i class="fab fa-google me-2"></i>Connect Gmail Account
                        </a>
                    </div>

                    {{-- Connected Accounts --}}
                    @if(auth()->user() && $connectedAccounts->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Connected Gmail Accounts</h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach($connectedAccounts as $account)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-envelope me-2"></i>{{ $account->email }}
                                        </span>
                                        <form action="{{ route('google.disconnect', $account->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add any client-side interactions or validations
    document.addEventListener('DOMContentLoaded', function() {
        // Automatic dismissal of alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush