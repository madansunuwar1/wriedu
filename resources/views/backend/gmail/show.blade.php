@extends('layouts.admin')

@section('content')
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(isset($email))
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Email Details</h2>
                <a href="{{ route('emails.index') }}" class="btn btn-secondary btn-sm">
                    Back to Inbox
                </a>
            </div>
            <div class="card-body">
                <div class="email-header mb-3">
                    <h3 class="card-title">{{ $email['subject'] ?? 'No Subject' }}</h3>
                    <div class="metadata text-muted">
                        <div><strong>From:</strong> {{ $email['from'] }}</div>
                        <div><strong>To:</strong> {{ $email['to'] }}</div>
                        <div><strong>Date:</strong> {{ $email['date'] }}</div>
                    </div>
                </div>
                <hr>
                <div class="email-body">
                    <div class="card-text">
                        {!! nl2br(e($email['body'])) !!}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            No email details available. Please try again.
        </div>
    @endif
</div>
@endsection