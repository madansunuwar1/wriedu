@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Mailboxes</div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('emails') || request()->is('emails/inbox') ? 'active' : '' }}" 
                               href="{{ route('emails.inbox', 'inbox') }}">
                                <input type="checkbox" disabled {{ isset($category) && $category == 'Inbox' ? 'checked' : '' }}> Inbox
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('emails/sent') ? 'active' : '' }}" 
                               href="{{ route('emails.sent', 'sent') }}">
                                <input type="checkbox" disabled {{ isset($category) && $category == 'Sent' ? 'checked' : '' }}> Sent
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('emails/draft') ? 'active' : '' }}" 
                               href="{{ route('emails.draft', 'draft') }}">
                                <input type="checkbox" disabled {{ isset($category) && $category == 'Draft' ? 'checked' : '' }}> Draft
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('emails/spam') ? 'active' : '' }}" 
                               href="{{ route('emails.spam', 'spam') }}">
                                <input type="checkbox" disabled {{ isset($category) && $category == 'Spam' ? 'checked' : '' }}> Spam
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('emails/trash') ? 'active' : '' }}" 
                               href="{{ route('emails.trash', 'trash') }}">
                                <input type="checkbox" disabled {{ isset($category) && $category == 'Trash' ? 'checked' : '' }}> Trash
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6>IMPORTANT</h6>
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('emails/starred') ? 'active' : '' }}" 
                                   href="{{ route('emails.starred', 'starred') }}">
                                    <input type="checkbox" disabled {{ isset($category) && $category == 'Starred' ? 'checked' : '' }}> Starred
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('emails/important') ? 'active' : '' }}" 
                                   href="{{ route('emails.important', 'important') }}">
                                    <input type="checkbox" disabled {{ isset($category) && $category == 'Important' ? 'checked' : '' }}> Important
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <h6>LABELS</h6>
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('emails/promotional') ? 'active' : '' }}" 
                                   href="{{ route('emails.promotional', 'promotional') }}">
                                    <input type="checkbox" disabled {{ isset($category) && $category == 'Promotional' ? 'checked' : '' }}> Promotional
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('emails/social') ? 'active' : '' }}" 
                                   href="{{ route('emails.social', 'social') }}">
                                    <input type="checkbox" disabled {{ isset($category) && $category == 'Social' ? 'checked' : '' }}> Social
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Email Listing -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    {{ isset($category) ? $category . ' Emails' : 'Your Emails' }}
                </div>
                
                <div class="card-body">
                    @if(empty($emails))
                        <p>No emails found in this category.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($emails as $email)
                                        <tr>
                                            <td>{{ $email['from'] }}</td>
                                            <td>{{ $email['subject'] }}</td>
                                            <td>{{ $email['date'] ?? '' }}</td>
                                            <td>
                                                <a href="{{ route('backend.gmail.show', $email['id']) }}" class="btn btn-sm btn-primary">
                                                    View
                                                </a>
                                                <button class="btn btn-sm btn-outline-secondary toggle-label" 
                                                    data-message-id="{{ $email['id'] }}" 
                                                    data-label="starred">
                                                    <i class="fas fa-star {{ isset($email['labels']) && in_array('STARRED', $email['labels']) ? 'text-warning' : '' }}"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.toggle-label').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const messageId = $(this).data('message-id');
        const label = $(this).data('label');
        const icon = $(this).find('i');
        
        $.ajax({
            url: `/emails/${messageId}/toggle-label/${label}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    icon.toggleClass('text-warning');
                }
            },
            error: function(xhr) {
                alert('Error toggling label');
            }
        });
    });
});
</script>
@endsection