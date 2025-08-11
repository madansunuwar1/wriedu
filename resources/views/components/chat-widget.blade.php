@extends('layouts.admin')
<link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">
@section('content')
<div class="card overflow-hidden chat-application">
    <div class="d-flex align-items-center justify-content-between gap-6 m-3 d-lg-none">
        <button class="btn btn-primary d-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#chat-sidebar" aria-controls="chat-sidebar">
            <i class="ti ti-menu-2 fs-5"></i>
        </button>
        <form class="position-relative w-100">
            <input type="text" class="form-control search-chat py-2 ps-5" id="mobile-user-search" placeholder="Search Contact">
            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
        </form>
    </div>

    <div class="d-flex" style="height: 85vh;">
        <!-- Left sidebar - User list -->
        <div class="w-30 d-none d-lg-block border-end user-chat-box" id="user-list-container">
            <div class="px-4 pt-9 pb-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <img id="current-user-avatar" src="{{ Auth::user()->avatar ? asset('avatars/' . Auth::user()->avatar) : asset('avatars/user-1.jpg') }}" alt="user" width="54" height="54" class="rounded-circle">
                            <span id="current-user-status" class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success">
                                <span class="visually-hidden">Status</span>
                            </span>
                        </div>
                        <div class="ms-3">
                            <h6 id="current-user-name" class="fw-semibold mb-2">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h6>
                            <p id="current-user-title" class="mb-0 fs-2">Status</p>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a class="text-dark fs-6 nav-icon-hover" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="ti ti-settings me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item" href="#"><i class="ti ti-logout me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
                <form class="position-relative mb-4">
                    <input type="text" id="user-search" class="form-control search-chat py-2 ps-5" placeholder="Search Contact">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </form>
                <div class="dropdown">
                    <a class="text-muted fw-semibold d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Recent Chats<i class="ti ti-chevron-down ms-1 fs-5"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Sort by time</a></li>
                        <li><a class="dropdown-item border-bottom" href="#">Sort by Unread</a></li>
                        <li><a class="dropdown-item" href="#">Hide favourites</a></li>
                    </ul>
                </div>
            </div>

            <div class="app-chat h-100">
                <ul id="user-list" class="chat-users mh-n100" data-simplebar>
                    <!-- Users will be loaded here via JavaScript -->
                    <div class="p-4 text-center text-gray-500">Loading users...</div>
                </ul>
            </div>
        </div>

        <!-- Right side - Chat area -->
        <div class="w-70 w-xs-100 chat-container" id="chat-container">
            <div class="chat-box-inner-part h-100">
                <!-- Default state when no chat is selected -->
                <div class="chat-not-selected h-100">
                    <div class="d-flex align-items-center justify-content-center h-100 p-5">
                        <div class="text-center">
                            <span class="text-primary">
                                <i class="ti ti-message-dots fs-10"></i>
                            </span>
                            <h6 class="mt-2">Select a chat to start messaging</h6>
                        </div>
                    </div>
                </div>

                <!-- Active chat area (hidden by default) -->
                <div class="chatting-box d-none h-100" id="active-chat">
                    <div class="p-9 border-bottom chat-meta-user d-flex align-items-center justify-content-between">
                        <div class="hstack gap-3 current-chat-user-name">
                            <button id="back-to-users" class="btn btn-sm d-lg-none">
                                <i class="ti ti-arrow-left"></i>
                            </button>
                            <div class="position-relative">
                                <img id="active-chat-avatar" src="https://via.placeholder.com/48" alt="user" width="48" height="48" class="rounded-circle">
                                <span id="active-chat-status" class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success">
                                    <span class="visually-hidden">Status</span>
                                </span>
                            </div>
                            <div>
                                <h6 id="active-chat-name" class="mb-1 fw-semibold"></h6>
                                <p id="active-chat-status-text" class="mb-0">Online</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex align-items-center">
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ti ti-phone"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ti ti-video"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ti ti-menu-2"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Messages container -->
                    <div class="chat-box h-100 mx-auto">
                        <div class="chat-box-inner p-9" id="messages-container" data-simplebar>
                            <!-- Messages will be loaded here -->
                        </div>

                        <!-- Message input area -->
                        <div class="px-9 py-6 border-top chat-send-message-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 w-85">
                                    <button id="attach-file" class="btn btn-sm text-dark">
                                        <i class="ti ti-paperclip"></i>
                                    </button>
                                    <input type="file" id="file-input" class="d-none">
                                    <input type="text" id="message-input" class="form-control message-type-box border-0 p-0 ms-2" placeholder="Type a Message">
                                    <button class="btn btn-sm text-dark">
                                        <i class="ti ti-mood-smile"></i>
                                    </button>
                                </div>
                                <button id="send-message" class="btn btn-primary">
                                    <i class="ti ti-send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile offcanvas sidebar -->
<div class="offcanvas offcanvas-start user-chat-box" tabindex="-1" id="chat-sidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Chats</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Mobile user list content will be cloned from desktop sidebar -->
    </div>
</div>

<!-- JavaScript -->
<script>
 document.addEventListener('DOMContentLoaded', function() {
    let activeChat = null;
    let users = [];
    let currentUserAvatar = '';
    let otherUserAvatar = '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // DOM elements
    const userListContainer = document.getElementById('user-list-container');
    const chatContainer = document.getElementById('chat-container');
    const userList = document.getElementById('user-list');
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendMessageButton = document.getElementById('send-message');
    const userSearchInput = document.getElementById('user-search');
    const fileInput = document.getElementById('file-input');
    const attachFileButton = document.getElementById('attach-file');
    const backToUsersButton = document.getElementById('back-to-users');
    const mobileSidebar = new bootstrap.Offcanvas(document.getElementById('chat-sidebar'));
    const activeChatArea = document.getElementById('active-chat');
    const noChatSelected = document.querySelector('.chat-not-selected');

    // Event listeners
    attachFileButton.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        const file = fileInput.files[0];
        if (file) {
            sendMessageWithFile(file);
        }
    });

    backToUsersButton.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            userListContainer.classList.add('d-block');
            chatContainer.classList.add('d-none');
        }
        showNoChatSelected();
    });

    // Initialize the chat
    function initializeChat() {
        loadUsers();
        setupEventListeners();
        setupEcho();
    }

    function setupEventListeners() {
        sendMessageButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Setup search functionality
        userSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            if (searchTerm === '') {
                renderUsers(users);
                return;
            }

            const filteredUsers = users.filter(user =>
                user.name.toLowerCase().includes(searchTerm) ||
                (user.last && user.last.toLowerCase().includes(searchTerm))
            );

            renderUsers(filteredUsers);
        });
    }

    // Load users list
    function loadUsers() {
        fetch('/chat/users')
            .then(response => response.json())
            .then(data => {
                users = data;
                renderUsers(data);
                // Clone user list to mobile sidebar
                document.querySelector('.offcanvas-body').innerHTML = userListContainer.innerHTML;
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                userList.innerHTML = '<div class="p-4 text-center text-gray-500">Error loading users</div>';
            });
    }

    // Render users list
    function renderUsers(users) {
        if (users.length === 0) {
            userList.innerHTML = '<div class="p-4 text-center text-gray-500">No users found</div>';
            return;
        }

        userList.innerHTML = users.map(user => `
            <li>
                <a href="#" class="px-4 py-3 d-flex align-items-start justify-content-between chat-user ${user.unread > 0 ? 'bg-light-subtle' : ''}"
                   data-user-id="${user.id}" onclick="openChat(${user.id})">
                    <div class="d-flex align-items-center">
                        <span class="position-relative">
                            <img src="${user.avatar}"
                                 alt="${user.name}" width="48" height="48" class="rounded-circle">
                            ${user.online ? '<span class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success"></span>' : ''}
                        </span>
                        <div class="ms-3 d-inline-block w-75">
                            <h6 class="mb-1 fw-semibold ${user.unread > 0 ? 'text-dark' : ''}">${user.name} ${user.last}</h6>
                            <span class="fs-3 text-truncate d-block ${user.unread > 0 ? 'text-dark fw-semibold' : 'text-body-color'}">
                                ${user.lastMessage || 'No messages yet'}
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="fs-2 mb-0 text-muted">${user.lastMessageTime || ''}</p>
                        ${user.unread > 0 ? `<span class="badge bg-danger rounded-pill">${user.unread}</span>` : ''}
                    </div>
                </a>
            </li>
        `).join('');

        // Update mobile sidebar
        const mobileSidebarElement = document.querySelector('.offcanvas-body'); // Store the element
        if (mobileSidebarElement) {
            const mobileUserList = document.createElement('ul');
            mobileUserList.className = 'chat-users p-0';
            mobileUserList.innerHTML = userList.innerHTML;
            mobileSidebarElement.innerHTML = '';
            mobileSidebarElement.appendChild(mobileUserList);
        }
    }

    // Open chat with a user
    window.openChat = function(userId) {
        activeChat = userId;
        const selectedUser = users.find(user => user.id == userId);

        // Fixed avatar URL handling
        let avatarUrl = '/assets/images/profile/user-1.jpg'; // Default
        if (selectedUser.avatar) {
            if (selectedUser.avatar.startsWith('http')) {
                avatarUrl = selectedUser.avatar;
            } else {
                avatarUrl = '/storage/' + selectedUser.avatar.replace(/^\/?storage\//, '');
            }
        }

        // Set active chat UI elements
        document.getElementById('active-chat-avatar').src = avatarUrl;
        document.getElementById('active-chat-name').textContent = `${selectedUser.name} ${selectedUser.last || ''}`;
        document.getElementById('active-chat-status').className = `position-absolute bottom-0 end-0 p-1 badge rounded-pill ${selectedUser.online ? 'bg-success' : 'bg-secondary'}`;
        document.getElementById('active-chat-status-text').textContent = selectedUser.online ? 'Online' : 'Offline';

        // Switch views
        noChatSelected.classList.add('d-none');
        activeChatArea.classList.remove('d-none');

        // Mobile handling
        if (window.innerWidth < 992) {
            userListContainer.classList.remove('d-block');
            chatContainer.classList.remove('d-none');
            mobileSidebar.hide();
        }

        loadConversation(userId);
        clearUnreadMessages(userId);

        // Listen for new messages in this chat
        listenForNewMessages(userId);
        initializeEchoListeners(userId);
    };

    function showNoChatSelected() {
        activeChat = null;
        activeChatArea.classList.add('d-none');
        noChatSelected.classList.remove('d-none');
    }

    function loadConversation(userId) {
        messagesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Loading messages...</div>';

        fetch(`/chat/conversations/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No messages yet. Start a conversation!</div>';
                } else {
                    // Store avatars with fixed URLs
                    currentUserAvatar = data.current_user_avatar
                        ? (data.current_user_avatar.startsWith('http')
                            ? data.current_user_avatar
                            : '/storage/' + data.current_user_avatar.replace(/^\/?storage\//, ''))
                        : '/assets/images/profile/user-1.jpg';

                    otherUserAvatar = data.other_user_avatar
                        ? (data.other_user_avatar.startsWith('http')
                            ? data.other_user_avatar
                            : '/storage/' + data.other_user_avatar.replace(/^\/?storage\//, ''))
                        : '/assets/images/profile/user-1.jpg';

                    renderMessages(data.messages);
                }
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                messagesContainer.innerHTML = `<div class="p-4 text-center text-gray-500">Error loading messages: ${error.message}</div>`;
            });
    }

    function renderMessages(messages) {
        messagesContainer.innerHTML = messages.map(msg => {
            const isAgent = msg.sender === 'agent';

            // FIXED AVATAR URL TRANSFORMATION
            const rawAvatar = isAgent ? currentUserAvatar : otherUserAvatar;
            let senderAvatar = '/assets/images/profile/user-1.jpg'; // default

            if (rawAvatar) {
                if (rawAvatar.startsWith('http://wrieducation.test/')) {
                    // Transform URL to include storage/avatars path
                    const filename = rawAvatar.split('/').pop();
                    senderAvatar = `http://wrieducation.test/storage/avatars/${filename}`;
                } else {
                    // Use as-is if it doesn't match our domain
                    senderAvatar = rawAvatar;
                }
            }

            const messageTime = formatMessageTime(msg.timestamp);
            const messageClass = isAgent ? 'bg-info-subtle text-dark' : 'text-bg-light';
            const alignmentClass = isAgent ? 'text-end' : '';
            const justifyClass = isAgent ? 'justify-content-end' : 'justify-content-start';

            let messageContent = '';
            if (msg.file_path) {
                if (msg.file_path.match(/\.(png|jpg|jpeg|gif)$/i)) {
                    messageContent = `
                        <div class="rounded-2 overflow-hidden">
                            <img src="/storage/${msg.file_path}" alt="File" class="w-100" onclick="openImagePopup('/storage/${msg.file_path}')">
                        </div>
                    `;
                } else {
                    const fileName = msg.file_path.split('/').pop();
                    messageContent = `
                        <a href="/storage/${msg.file_path}" download="${fileName}" class="p-2 ${messageClass} rounded-1 d-inline-block fs-3">
                            Download ${fileName}
                        </a>
                    `;
                }
            } else {
                messageContent = `
                    <div class="p-2 ${messageClass} rounded-1 d-inline-block fs-3">
                        ${msg.message}
                    </div>
                `;
            }

            const statusIndicator = isAgent ? `
                <div class="message-status text-xs text-muted">
                    ${getStatusIndicator(msg.status, msg.timestamp)}
                </div>
            ` : '';

            return `
                <div class="hstack gap-3 align-items-start mb-7 ${justifyClass}"
                     data-message-id="${msg.id}"
                     data-status="${msg.status || 'sent'}">
                    ${!isAgent ? `
                    <img src="${senderAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    ` : ''}
                    <div class="${alignmentClass}">
                        <h6 class="fs-2 text-muted">
                            ${msg.sender_name || (isAgent ? 'You' : 'User')}, ${messageTime}
                        </h6>
                        ${messageContent}
                        ${statusIndicator}
                    </div>
                    ${isAgent ? `
                    <img src="${senderAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    ` : ''}
                </div>
            `;
        }).join('');
    }

    // Format message time
    function formatMessageTime(timestamp) {
        if (!timestamp) return 'Just now';

        const msgDate = new Date(timestamp);
        if (isNaN(msgDate.getTime())) return 'Recent';

        const now = new Date();
        const timeDiff = now - msgDate;
        const oneHour = 60 * 60 * 1000;
        const oneDay = 24 * oneHour;
        const oneWeek = 7 * oneDay;

        if (timeDiff < oneHour) {
            const minutesAgo = Math.floor(timeDiff / (60 * 1000));
            return minutesAgo <= 1 ? 'Just now' : `${minutesAgo} min ago`;
        } else if (timeDiff < oneDay) {
            return msgDate.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        } else if (timeDiff < oneWeek) {
            return msgDate.toLocaleDateString([], {
                    weekday: 'short'
                }) + ' ' +
                msgDate.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        } else {
            return msgDate.toLocaleDateString([], {
                    month: 'short',
                    day: 'numeric'
                }) + ' ' +
                msgDate.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }
    }

    // Get status indicator
    function getStatusIndicator(status, timestamp = new Date()) {
        if (!status) return '';
        status = String(status).trim().toLowerCase();

        const formattedTime = formatMessageTime(timestamp);
        switch (status) {
            case 'sending':
                return `<span class="status-indicator">Sending...</span>`;
            case 'sent':
                return `<span class="status-indicator">Sent</span>`;
            case 'delivered':
                return `<span class="status-indicator">Delivered</span>`;
            case 'seen':
                return `<span class="status-indicator text-success">Seen</span>`;
            default:
                return '';
        }
    }

    // Send message
    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message || !activeChat) return;

        messageInput.value = '';

        const tempMsgId = 'temp-' + Date.now();
        const now = new Date();

        const tempMessageElement = document.createElement('div');
        tempMessageElement.className = 'hstack gap-3 align-items-start mb-7 justify-content-end';
        tempMessageElement.setAttribute('data-message-id', tempMsgId);
        tempMessageElement.setAttribute('data-status', 'sending');
        tempMessageElement.innerHTML = `
            <div class="text-end">
                <h6 class="fs-2 text-muted">You, Just now</h6>
                <div class="p-2 bg-info-subtle text-dark rounded-1 d-inline-block fs-3">
                    ${message}
                </div>
                <div class="message-status text-xs text-muted">
                    ${getStatusIndicator('sending', now)}
                </div>
            </div>
            <img src="${currentUserAvatar}" alt="user" width="40" height="40" class="rounded-circle">
        `;
        messagesContainer.appendChild(tempMessageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    to_user_id: activeChat,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                const tempMsg = document.querySelector(`[data-message-id="${tempMsgId}"]`);
                if (tempMsg) {
                    tempMsg.setAttribute('data-message-id', data.id);
                    updateMessageStatus(data.id, 'sent', data.timestamp);
                }

                updateUserLastMessage(activeChat, message, data.timestamp);

                if (users.find(user => user.id == activeChat).online) {
                    markMessageAsDelivered(data.id);
                    updateMessageStatus(data.id, 'delivered', data.timestamp);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                const tempMsg = document.querySelector(`[data-message-id="${tempMsgId}"]`);
                if (tempMsg) {
                    const statusElement = tempMsg.querySelector('.message-status');
                    if (statusElement) {
                        statusElement.innerHTML = '<span class="text-danger">Failed to send</span>';
                    }
                }
            });
    }

    // Send message with file
    function sendMessageWithFile(file) {
        if (!activeChat) return;

        const formData = new FormData();
        formData.append('to_user_id', activeChat);
        formData.append('file', file);

        // Create temporary message element to show uploading status
        const tempMsgId = 'temp-file-' + Date.now();
        const now = new Date();
        const tempMessageElement = document.createElement('div');
        tempMessageElement.className = 'hstack gap-3 align-items-start mb-7 justify-content-end';
        tempMessageElement.setAttribute('data-message-id', tempMsgId);

        // Different display for images vs other files
        if (file.type.startsWith('image/')) {
            tempMessageElement.innerHTML = `
                <div class="text-end">
                    <h6 class="fs-2 text-muted">You, Just now</h6>
                    <div class="rounded-2 overflow-hidden position-relative">
                        <img src="${URL.createObjectURL(file)}" alt="Sending Image" class="w-100" style="max-width: 250px;">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-50">
                            <span class="text-white">Uploading...</span>
                        </div>
                    </div>
                    <div class="message-status text-xs text-muted">
                        ${getStatusIndicator('sending', now)}
                    </div>
                </div>
                <img src="${currentUserAvatar}" alt="user" width="40" height="40" class="rounded-circle">
            `;
        } else {
            tempMessageElement.innerHTML = `
                <div class="text-end">
                    <h6 class="fs-2 text-muted">You, Just now</h6>
                    <div class="p-2 bg-info-subtle text-dark rounded-1 d-inline-block fs-3">
                        Uploading: ${file.name}
                    </div>
                    <div class="message-status text-xs text-muted">
                        ${getStatusIndicator('sending', now)}
                    </div>
                </div>
                <img src="${currentUserAvatar}" alt="user" width="40" height="40" class="rounded-circle">
            `;
        }

        messagesContainer.appendChild(tempMessageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Remove temporary message
                const tempMsg = document.querySelector(`[data-message-id="${tempMsgId}"]`);
                if (tempMsg) {
                    messagesContainer.removeChild(tempMsg);
                }

                // Create permanent message with the right format
                const messageElement = document.createElement('div');
                messageElement.className = 'hstack gap-3 align-items-start mb-7 justify-content-end';
                messageElement.setAttribute('data-message-id', data.id);
                messageElement.setAttribute('data-status', 'sent');

                if (file.type.startsWith('image/')) {
                    messageElement.innerHTML = `
                        <div class="text-end">
                            <h6 class="fs-2 text-muted">You, ${formatMessageTime(data.timestamp)}</h6>
                            <div class="rounded-2 overflow-hidden">
                                <img src="/storage/${data.file_path}" alt="Sent Image" class="w-100" style="max-width: 250px;" onclick="openImagePopup('/storage/${data.file_path}')">
                            </div>
                            <div class="message-status text-xs text-muted">
                                ${getStatusIndicator('sent', data.timestamp)}
                            </div>
                        </div>
                        <img src="${currentUserAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    `;
                } else {
                    const fileName = file.name;
                    messageElement.innerHTML = `
                        <div class="text-end">
                            <h6 class="fs-2 text-muted">You, ${formatMessageTime(data.timestamp)}</h6>
                            <a href="/storage/${data.file_path}" download="${fileName}" class="p-2 bg-info-subtle text-dark rounded-1 d-inline-block fs-3">
                                Download ${fileName}
                            </a>
                            <div class="message-status text-xs text-muted">
                                ${getStatusIndicator('sent', data.timestamp)}
                            </div>
                        </div>
                        <img src="${currentUserAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    `;
                }

                messagesContainer.appendChild(messageElement);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                updateUserLastMessage(activeChat, file.type.startsWith('image/') ? '[Image]' : `[File: ${file.name}]`, data.timestamp);

                if (users.find(user => user.id == activeChat).online) {
                    markMessageAsDelivered(data.id);
                    updateMessageStatus(data.id, 'delivered', data.timestamp);
                }
            })
            .catch(error => {
                console.error('Error sending message with file:', error);
                const tempMsg = document.querySelector(`[data-message-id="${tempMsgId}"]`);
                if (tempMsg) {
                    const statusElement = tempMsg.querySelector('.message-status');
                    if (statusElement) {
                        statusElement.innerHTML = '<span class="text-danger">Failed to upload</span>';
                    }

                    // Remove the "uploading" overlay for images
                    if (file.type.startsWith('image/')) {
                        const overlay = tempMsg.querySelector('.position-absolute');
                        if (overlay) {
                            overlay.innerHTML = '<span class="text-white bg-danger p-1">Upload failed</span>';
                        }
                    }
                }
            });
    }

    // Update message status
    function updateMessageStatus(messageId, status, timestamp = new Date()) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            const statusElement = messageElement.querySelector('.message-status');
            if (statusElement) {
                statusElement.innerHTML = getStatusIndicator(status, timestamp);
                messageElement.setAttribute('data-status', status);
            }
        }
    }

    // Update user's last message
    function updateUserLastMessage(userId, message, timestamp) {
        const userIndex = users.findIndex(u => u.id == userId);
        if (userIndex !== -1) {
            users[userIndex].lastMessage = message;
            users[userIndex].lastMessageTime = formatMessageTime(timestamp);
            renderUsers(users);
        }
    }

    // Clear unread messages
    function clearUnreadMessages(userId) {
        const userIndex = users.findIndex(u => u.id == userId);
        if (userIndex !== -1 && users[userIndex].unread > 0) {
            users[userIndex].unread = 0;
            markMessageAsRead(userId);
            renderUsers(users);
        }
    }

    // Mark message as read
    function markMessageAsRead(userId) {
        fetch('/chat/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                from_user_id: userId
            })
        }).catch(error => {
            console.error('Error marking messages as read:', error);
        });
    }

    // Mark message as delivered
    function markMessageAsDelivered(messageId) {
        fetch('/chat/mark-delivered', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                message_id: messageId
            })
        }).catch(error => {
            console.error('Error marking message as delivered:', error);
        });
    }

    // Image popup functions
    window.openImagePopup = function(imageSrc) {
        const popup = document.createElement('div');
        popup.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50';
        popup.innerHTML = `
            <div class="relative max-w-full max-h-full">
                <img src="${imageSrc}" alt="Popup Image" class="max-w-full max-h-full">
                <button class="absolute top-2 right-2 bg-white rounded-full p-2" onclick="closeImagePopup()">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        `;
        document.body.appendChild(popup);
    };

    window.closeImagePopup = function() {
        const popup = document.querySelector('.fixed.inset-0.flex.items-center.justify-center.bg-black.bg-opacity-75.z-50');
        if (popup) {
            document.body.removeChild(popup);
        }
    };

    // Setup Laravel Echo
    function setupEcho() {
        import('laravel-echo').then(({ Echo }) => {
            window.Pusher = require('pusher-js');

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                forceTLS: true
            });

            // Listen for new messages globally
             window.Echo.private(`chat`)
                .listen('NewMessage', (e) => {
                    const message = e.message;

                    // Update the user list with the new message count and last message
                    updateUserLastMessage(message.sender_id, message.message, message.timestamp);

                    // Check if the message belongs to the active chat
                    if (activeChat && (message.sender_id == activeChat || message.receiver_id == activeChat)) {
                         // Append the message to the active chat
                        appendNewMessages([message]);
                        markMessageAsRead(message.sender_id);
                    } else {
                        // If the active chat is not the sender/receiver, update the unread count
                        const userIndex = users.findIndex(u => u.id == message.sender_id);
                        if (userIndex !== -1) {
                            users[userIndex].unread = (users[userIndex].unread || 0) + 1;
                            renderUsers(users); // Re-render user list to show the new unread count
                        }
                    }
                });
        }).catch(error => {
            console.error('Error setting up Laravel Echo:', error);
        });
    }

    // Listen for new messages in the active chat (Optional, but keep it for robustness)
    function listenForNewMessages(userId) {
        window.Echo.private(`chat.${userId}`)
            .listen('NewMessage', (e) => {
                appendNewMessages([e.message]);
                markMessageAsRead(e.message.sender_id);
            });
    }

    // Append new messages to the chat window
   function appendNewMessages(messages) {
        if (!messages || messages.length === 0 || !activeChat) return;

        // Make sure that messages are for the currently active chat
        const filteredMessages = messages.filter(msg => (msg.sender_id == activeChat || msg.receiver_id == activeChat));

        if (filteredMessages.length === 0) return;

        const renderedMessages = filteredMessages.map(msg => {
            const isAgent = msg.sender_id == window.Laravel.userId;

            const rawAvatar = isAgent ? currentUserAvatar : otherUserAvatar;
            let senderAvatar = '/assets/images/profile/user-1.jpg';

            if (rawAvatar) {
                if (rawAvatar.startsWith('http://wrieducation.test/')) {
                    const filename = rawAvatar.split('/').pop();
                    senderAvatar = `http://wrieducation.test/storage/avatars/${filename}`;
                } else {
                    senderAvatar = rawAvatar;
                }
            }

            const messageTime = formatMessageTime(msg.timestamp);
            const messageClass = isAgent ? 'bg-info-subtle text-dark' : 'text-bg-light';
            const alignmentClass = isAgent ? 'text-end' : '';
            const justifyClass = isAgent ? 'justify-content-end' : 'justify-content-start';

            let messageContent = '';
            if (msg.file_path) {
                if (msg.file_path.match(/\.(png|jpg|jpeg|gif)$/i)) {
                    messageContent = `
                        <div class="rounded-2 overflow-hidden">
                            <img src="/storage/${msg.file_path}" alt="File" class="w-100" onclick="openImagePopup('/storage/${msg.file_path}')">
                        </div>
                    `;
                } else {
                    const fileName = msg.file_path.split('/').pop();
                    messageContent = `
                        <a href="/storage/${msg.file_path}" download="${fileName}" class="p-2 ${messageClass} rounded-1 d-inline-block fs-3">
                            Download ${fileName}
                        </a>
                    `;
                }
            } else {
                messageContent = `
                    <div class="p-2 ${messageClass} rounded-1 d-inline-block fs-3">
                        ${msg.message}
                    </div>
                `;
            }

            const statusIndicator = isAgent ? `
                <div class="message-status text-xs text-muted">
                    ${getStatusIndicator(msg.status, msg.timestamp)}
                </div>
            ` : '';

            return `
                <div class="hstack gap-3 align-items-start mb-7 ${justifyClass}"
                     data-message-id="${msg.id}"
                     data-status="${msg.status || 'sent'}">
                    ${!isAgent ? `
                    <img src="${senderAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    ` : ''}
                    <div class="${alignmentClass}">
                        <h6 class="fs-2 text-muted">
                            ${msg.sender_name || (isAgent ? 'You' : 'User')}, ${messageTime}
                        </h6>
                        ${messageContent}
                        ${statusIndicator}
                    </div>
                    ${isAgent ? `
                    <img src="${senderAvatar}" alt="user" width="40" height="40" class="rounded-circle">
                    ` : ''}
                </div>
            `;
        }).join('');

        messagesContainer.innerHTML += renderedMessages;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    //Implement listener to the active chat
    function initializeEchoListeners(userId) {
        Echo.private(`chat.${userId}`)
            .listen('NewMessage', (e) => {
                handleIncomingMessage(e.message);
            })
            .listen('MessageStatusUpdated', (e) => {
                handleMessageStatusUpdate(e.data);
            })
           .listen('UserTyping', (e) => {
                handleTypingIndicator(e.from_user_id);
            });

        console.log('WebSocket/Pusher listeners need to be implemented based on your setup');
    }

    // Handle incoming messages
    function handleIncomingMessage(message) {
        if (activeChat && (message.sender_id == activeChat || message.receiver_id == activeChat)) {
            appendNewMessages([message]);
            markMessageAsRead(message.sender_id);
        } else {
            // Update unread count if the message is not in the active chat
            const userIndex = users.findIndex(u => u.id == message.sender_id);
            if (userIndex !== -1) {
                users[userIndex].unread = (users[userIndex].unread || 0) + 1;
                renderUsers(users); // Re-render user list to show the new unread count
            }
        }
    }

    // Handle message status updates
    function handleMessageStatusUpdate(data) {
        updateMessageStatus(data.message_id, data.status, data.timestamp);
    }

    // Handle typing indicator (example)
    function handleTypingIndicator(fromUserId) {
        // Implement your typing indicator logic here
        // This function should show a visual indicator that the user is typing
        console.log(`User ${fromUserId} is typing...`);
    }

    // Initialize the chat
    initializeChat();
});
</script>
@endsection