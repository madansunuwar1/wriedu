<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        .styled-email-content * {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
            box-sizing: border-box;
        }
        .styled-email-content {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            word-break: break-word;
        }
        .styled-email-content p {
            margin-bottom: 1em;
        }
        .styled-email-content h1 {
            font-size: 2em;
            margin-bottom: 0.5em;
        }
        .styled-email-content h2 {
            font-size: 1.5em;
            margin-bottom: 0.5em;
        }
        .styled-email-content h3 {
            font-size: 1.2em;
            margin-bottom: 0.5em;
        }
        .styled-email-content a {
            color: #007bff;
            text-decoration: none;
        }
        .styled-email-content a:hover {
            text-decoration: underline;
        }
        .styled-email-content ul, .styled-email-content ol {
            margin-left: 20px;
            margin-bottom: 1em;
        }
        .styled-email-content li {
            margin-bottom: 0.5em;
        }
        .styled-email-content img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .styled-email-content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }
        .styled-email-content th, .styled-email-content td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .styled-email-content th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .styled-email-content blockquote {
            border-left: 5px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .chat-user {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .chat-user:hover {
            background-color: #f8f9fa;
        }
        .chat-list {
            display: none;
        }
        .chat-list.active-chat {
            display: block;
        }
        .ti-star, .ti-alert-circle {
            cursor: pointer;
            transition: color 0.2s;
        }
        .ti-star.text-warning, .ti-alert-circle.text-warning {
            color: #ffc107 !important;
        }
        .mh-n100 {
            max-height: calc(100vh - 200px);
        }
        .mh-n150 {
            max-height: calc(100vh - 150px);
        }
        .nav-icon-hover:hover {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
        }
        .bg-hover-light-black:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .bg-hover-primary:hover {
            color: #0d6efd !important;
        }
        .chat-application {
            height: calc(100vh - 60px);
            display: flex;
        }
        .left-part {
            width: 280px;
        }
        .min-width-340 {
            min-width: 340px;
        }
        .email-list-container {
            flex: 1;
            overflow-y: auto;
        }
        .email-content-container {
            flex: 2;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<div class="card overflow-hidden chat-application">
    <div class="d-flex align-items-center justify-content-between gap-6 m-3 d-lg-none">
        <button class="btn btn-primary d-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#chat-sidebar"
                aria-controls="chat-sidebar">
            <i class="ti ti-menu-2 fs-5"></i>
        </button>
        <form class="position-relative w-100">
            <input type="text" class="form-control search-chat py-2 ps-5" id="text-srh" placeholder="Search Contact">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
        </form>
    </div>
    <div class="d-flex w-100">
        <div class="left-part border-end w-20 flex-shrink-0 d-none d-lg-block">
            <div class="px-9 pt-4 pb-3">
                <button type="button" class="btn btn-primary fw-semibold py-8 w-100" data-bs-toggle="modal"
                        data-bs-target="#composeModal">
                    Compose
                </button>
            </div>
            <ul class="list-group mh-n100" data-simplebar="">
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.inbox', 'inbox') }}">
                        <i class="ti ti-inbox fs-5"></i>Inbox
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.sent', 'sent') }}">
                        <i class="ti ti-brand-telegram fs-5"></i>Sent
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.draft', 'draft') }}">
                        <i class="ti ti-file-text fs-5"></i>Draft
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.spam', 'spam') }}">
                        <i class="ti ti-inbox fs-5"></i>Spam
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.trash', 'trash') }}">
                        <i class="ti ti-trash fs-5"></i>Trash
                    </a>
                </li>
                <li class="border-bottom my-3"></li>
                <li class="fw-semibold text-dark text-uppercase mx-9 my-2 px-3 fs-2">
                    IMPORTANT
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.starred', 'starred') }}">
                        <i class="ti ti-star fs-5"></i>Starred
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.important', 'important') }}">
                        <i class="ti ti-badge fs-5"></i>Important
                    </a>
                </li>
                <li class="border-bottom my-3"></li>
                <li class="fw-semibold text-dark text-uppercase mx-9 my-2 px-3 fs-2">
                    LABELS
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.promotional', 'promotional') }}">
                        <i class="ti ti-bookmark fs-5 text-primary"></i>Promotional
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.social', 'social') }}">
                        <i class="ti ti-bookmark fs-5 text-warning"></i>Social
                    </a>
                </li>
                <li class="list-group-item border-0 p-0 mx-9">
                    <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
                       href="{{ route('emails.health', 'health') }}">
                        <i class="ti ti-bookmark fs-5 text-success"></i>Health
                    </a>
                </li>
            </ul>
        </div>
        <div class="d-flex w-100">
            <div class="min-width-340 email-list-container">
                <div class="border-end user-chat-box h-100">
                    <div class="px-4 pt-9 pb-6 d-none d-lg-block">
                        <form class="position-relative">
                            <input type="text" class="form-control search-chat py-2 ps-5" id="text-srh"
                                   placeholder="Search">
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </form>
                    </div>
                    <div class="app-chat">
                        <ul class="chat-users mh-n100" data-simplebar="">
                            @if(count($emails) > 0)
                                @foreach($emails as $email)
                                    <li>
                                        <a href="javascript:void(0)"
                                           class="px-4 py-3 bg-hover-light-black d-flex align-items-start chat-user bg-light-subtle"
                                           id="chat_user_{{ $email['id'] }}" data-user-id="{{ $email['id'] }}">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="flexCheckDefault">
                                            </div>
                                            <div class="position-relative w-100 ms-2">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="mb-0">{{ $email['from'] }}</h6>
                                                    <span class="badge text-bg-primary">{{ $email['label'] }}</span>
                                                </div>
                                                <h6 class="fw-semibold text-dark">
                                                    {{ $email['subject'] }}
                                                </h6>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                            <span>
                                                                <i class="ti ti-star fs-4 me-2 text-dark toggle-label"
                                                                   data-message-id="{{ $email['id'] }}"
                                                                   data-label="starred"></i>
                                                            </span>
                                                        <span class="d-block">
                                                                <i class="ti ti-alert-circle text-muted toggle-label"
                                                                   data-message-id="{{ $email['id'] }}"
                                                                   data-label="important"></i>
                                                            </span>
                                                    </div>
                                                    <p class="mb-0 fs-2 text-muted">{{ $email['date'] }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="px-4 py-3 text-center">
                                    <p class="text-muted">No emails found in this category.</p>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="w-100 email-content-container">
                <div class="chat-container h-100 w-100">
                    <div class="chat-box-inner-part h-100">
                        <div class="chatting-box app-email-chatting-box">
                            <div class="p-9 py-3 border-bottom chat-meta-user">
                                <ul class="list-unstyled mb-0 d-flex align-items-center">
                                    <li class="d-lg-none d-block">
                                        <a class="text-dark back-btn px-2 fs-5 bg-hover-primary nav-icon-hover position-relative z-index-5"
                                           href="javascript:void(0)">
                                            <i class="ti ti-arrow-left"></i>
                                        </a>
                                    </li>
                                    <li class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Star">
                                        <a class="text-dark px-2 fs-5 bg-hover-primary nav-icon-hover position-relative z-index-5"
                                           href="javascript:void(0)">
                                            <i class="ti ti-star"></i>
                                        </a>
                                    </li>
                                    <li class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="important">
                                        <a class="d-block text-dark px-2 fs-5 bg-hover-primary nav-icon-hover position-relative z-index-5"
                                           href="javascript:void(0)">
                                            <i class="ti ti-alert-circle"></i>
                                        </a>
                                    </li>
                                    <li class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Delete">
                                        <a class="text-dark px-2 fs-5 bg-hover-primary nav-icon-hover position-relative z-index-5"
                                           href="javascript:void(0)">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="position-relative overflow-hidden">
                                <div class="position-relative">
                                    <div class="chat-box email-box mh-n100 p-9" data-simplebar="init">
                                        @if(count($emails) > 0)
                                            @foreach($emails as $email)
                                                <div class="chat-list chat" data-user-id="{{ $email['id'] }}"
                                                     style="display: none;">
                                                    <div class="email-header border-bottom pb-3 mb-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h2 class="fw-semibold mb-1" style="font-size: 1.375rem;">{{ $email['subject'] }}</h2>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-sm btn-outline-secondary p-1">
                                                                    <i class="ti ti-star fs-5"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-secondary p-1">
                                                                    <i class="ti ti-trash fs-5"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-start gap-3">
                                                            <img src="../assets/images/profile/user-8.jpg" alt="sender"
                                                                 width="40" height="40" class="rounded-circle mt-1">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                                    <span class="fw-semibold">{{ $email['from'] }}</span>
                                                                    <span class="text-muted fs-2"><{{ $email['email'] }}></span>
                                                                </div>
                                                                <div class="text-muted fs-2 d-flex flex-wrap align-items-center gap-2">
                                                                    <span>to me</span>
                                                                    <span>•</span>
                                                                    <span>{{ $email['date'] }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="email-body mb-4 styled-email-content"
                                                         style="font-size: 0.9375rem; line-height: 1.6; white-space: pre-line;">
                                                       @php
                                                           $isHtml = (strpos($email['body'], '<html') !== false || strpos($email['body'], '<body') !== false);
                                                       @endphp
                                                       @if ($isHtml)
                                                           <iframe srcdoc="{!! htmlspecialchars($email['body']) !!}" style="width:100%; height:400px; border:none;"></iframe>
                                                       @else
                                                            <pre style="white-space: pre-wrap;">{{ $email['body'] }}</pre>
                                                       @endif
                                                    </div>
                                                    <div class="email-signature mb-4 pt-2 border-top">
                                                        <p class="mb-1 text-muted">Regards,</p>
                                                        <p class="fw-semibold">{{ $email['from'] }}
                                                            <<{{ $email['email'] }}></p>
                                                    </div>
                                                    <div class="email-footer border-top pt-3">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button
                                                                class="btn btn-light btn-sm d-flex align-items-center gap-1 px-3 py-1">
                                                                <i class="ti ti-arrow-back-up fs-5"></i>
                                                                <span>Reply</span>
                                                            </button>
                                                            <button
                                                                class="btn btn-light btn-sm d-flex align-items-center gap-1 px-3 py-1">
                                                                <i class="ti ti-arrow-forward-up fs-5"></i>
                                                                <span>Forward</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="composeModalLabel">New Message</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="composeForm" action="{{ route('emails.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="email" class="form-control" id="to" name="to" placeholder="To" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"
                               required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" id="body" name="body" rows="10" placeholder="Message"
                                  required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="composeForm" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-start user-chat-box" tabindex="-1" id="chat-sidebar"
     aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">
            Email
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="px-9 pt-4 pb-3">
        <button class="btn btn-primary fw-semibold py-8 w-100" data-bs-toggle="modal"
                data-bs-target="#composeModal">
            Compose
        </button>
    </div>
    <ul class="list-group h-n150" data-simplebar="">
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.inbox', 'inbox') }}">
                <i class="ti ti-inbox fs-5"></i>Inbox
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.sent', 'sent') }}">
                <i class="ti ti-brand-telegram fs-5"></i>Sent
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.draft', 'draft') }}">
                <i class="ti ti-file-text fs-5"></i>Draft
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.spam', 'spam') }}">
                <i class="ti ti-inbox fs-5"></i>Spam
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.trash', 'trash') }}">
                <i class="ti ti-trash fs-5"></i>Trash
            </a>
        </li>
        <li class="border-bottom my-3"></li>
        <li class="fw-semibold text-dark text-uppercase mx-9 my-2 px-3 fs-2">
            IMPORTANT
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.starred', 'starred') }}">
                <i class="ti ti-star fs-5"></i>Starred
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.important', 'important') }}">
                <i class="ti ti-badge fs-5"></i>Important
            </a>
        </li>
        <li class="border-bottom my-3"></li>
        <li class="fw-semibold text-dark text-uppercase mx-9 my-2 px-3 fs-2">
            LABELS
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.promotional', 'promotional') }}">
                <i class="ti ti-bookmark fs-5 text-primary"></i>Promotional
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.social', 'social') }}">
                <i class="ti ti-bookmark fs-5 text-warning"></i>Social
            </a>
        </li>
        <li class="list-group-item border-0 p-0 mx-9">
            <a class="d-flex align-items-center gap-6 list-group-item-action text-dark px-3 py-8 mb-1 rounded-1"
               href="{{ route('emails.health', 'health') }}">
                <i class="ti ti-bookmark fs-5 text-success"></i>Health
            </a>
        </li>
    </ul>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script>
    $(document).ready(function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        $('.chat-user').click(function () {
            const userId = $(this).data('user-id');
            displayEmailContent(userId);
        });
        function displayEmailContent(userId) {
    $('.chat-list').hide();
    $('.chat-list[data-user-id="' + userId + '"]').show(); // Fixed selector
    $('.chat-list').removeClass('active-chat');
    $('.chat-list[data-user-id="' + userId + '"]').addClass('active-chat'); // Fixed selector
}
        $('.chat-list').hide();
        $('.back-btn').click(function () {
            $('.chat-list').removeClass('active-chat');
        });
        $('.ti-star').click(function (e) {
            e.stopPropagation();
            const messageId = $(this).closest('.chat-user').data('user-id');
            toggleLabel(messageId, 'starred', $(this));
        });
        $('.ti-alert-circle').click(function (e) {
            e.stopPropagation();
            const messageId = $(this).closest('.chat-user').data('user-id');
            toggleLabel(messageId, 'important', $(this));
        });
        $('.ti-trash').click(function (e) {
            e.preventDefault();
            const activeChat = $('.chat-list.active-chat');
            if (activeChat.length) {
                const messageId = activeChat.data('user-id');
                if (confirm('Are you sure you want to move this email to trash?')) {
                    moveToTrash(messageId);
                }
            }
        });
        function toggleLabel(messageId, label, element) {
            $.ajax({
                url: `/emails/${messageId}/toggle-label/${label}`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        element.toggleClass('text-warning');
                    }
                },
                error: function (xhr) {
                    alert('Error toggling label');
                }
            });
        }
        function moveToTrash(messageId) {
            $.ajax({
               url: `/emails/${messageId}/move-to-trash`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = "{{ route('emails.inbox') }}";
                    }
                },
                error: function (xhr) {
                    alert('Error moving email to trash');
                }
            });
        }
        $('#composeForm').submit(function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#composeModal').modal('hide');
                    window.location.href = "{{ route('emails.sent') }}";
                },
                error: function (xhr) {
                    alert('Error sending email: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
<script src="../../../npm/iconify-icon%401.0.8/dist/iconify-icon.min.js"></script>
<script src="../assets/js/highlights/highlight.min.js"></script>
<script src="../assets/js/apps/chat.js"></script>
</body>
</html>