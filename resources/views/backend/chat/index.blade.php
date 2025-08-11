@extends('layouts.admin')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Full Chat Application</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Base styles from original widget */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .hidden { display: none; }
        
        /* Status indicators */
        .status-indicator { display: inline-block; margin-left: 4px; }
        .message-status { min-height: 1.5em; display: block; }
        
        /* Typing indicator */
        .typing-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #ddd;
            border-radius: 50%;
            animation: typing 1.5s infinite;
        }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }
        
        /* Chat image styles */
        .chat-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            padding: 5px 0;
            transition: transform 0.3s ease;
        }
        .chat-image:hover { transform: scale(1.05); }
        .image-container { text-align: center; margin-bottom: 10px; }
        
        /* Layout styles from reference design */
        .chat-application { height: calc(100vh - 2rem); }
        .user-chat-box { width: 30%; border-right: 1px solid #e5e7eb; }
        .chat-container { width: 70%; }
        .chat-box-inner { height: calc(100% - 120px); overflow-y: auto; }
        .chat-send-message-footer { border-top: 1px solid #e5e7eb; }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .user-chat-box { width: 35%; }
            .chat-container { width: 65%; }
        }
        @media (max-width: 768px) {
            .user-chat-box { width: 100%; display: none; }
            .chat-container { width: 100%; }
            .user-chat-box.d-block { display: block !important; }
            .chat-container.d-none { display: none !important; }
        }
        
        /* Custom scrollbar */
        [data-simplebar] { height: 100%; }
    </style>
</head>
<body class="bg-gray-100">
<div class="card overflow-hidden chat-application m-4">
    <!-- Mobile header with search and menu -->
    <div class="d-flex align-items-center justify-content-between gap-6 m-3 d-lg-none">
        <button class="btn btn-primary d-flex" type="button" id="mobile-menu-button">
            <i class="ri-menu-2-line fs-5"></i>
        </button>
        <form class="position-relative w-100">
            <input type="text" class="form-control search-chat py-2 ps-5" id="mobile-user-search" placeholder="Search Contact">
            <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
        </form>
    </div>
    
    <div class="d-flex h-100">
        <!-- Left sidebar - User list -->
        <div class="w-30 d-none d-lg-block border-end user-chat-box" id="user-list-container">
            <div class="px-4 pt-9 pb-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <img id="current-user-avatar" src="https://via.placeholder.com/54" alt="user" width="54" height="54" class="rounded-circle">
                            <span id="current-user-status" class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success">
                                <span class="visually-hidden">Status</span>
                            </span>
                        </div>
                        <div class="ms-3">
                            <h6 id="current-user-name" class="fw-semibold mb-2">User Name</h6>
                            <p id="current-user-title" class="mb-0 fs-2">Status</p>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a class="text-dark fs-6 nav-icon-hover" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-2-line"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="ri-settings-line me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item" href="#"><i class="ri-logout-circle-line me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
                <form class="position-relative mb-4">
                    <input type="text" id="user-search" class="form-control search-chat py-2 ps-5" placeholder="Search Contact">
                    <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </form>
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
                                <i class="ri-message-2-line fs-10"></i>
                            </span>
                            <h6 class="mt-2">Select a chat to start messaging</h6>
                        </div>
                    </div>
                </div>
                
                <!-- Active chat area (hidden by default) -->
                <div class="chatting-box d-none h-100" id="active-chat">
                    <div class="p-3 border-bottom chat-meta-user d-flex align-items-center justify-content-between">
                        <div class="hstack gap-3 current-chat-user-name">
                            <button id="back-to-users" class="btn btn-sm d-lg-none">
                                <i class="ri-arrow-left-line"></i>
                            </button>
                            <div class="position-relative">
                                <img id="active-chat-avatar" src="https://via.placeholder.com/48" alt="user" width="48" height="48" class="rounded-circle">
                                <span id="active-chat-status" class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success">
                                    <span class="visually-hidden">Status</span>
                                </span>
                            </div>
                            <div>
                                <h6 id="active-chat-name" class="mb-1 fw-semibold"></h6>
                                <p id="active-chat-status-text" class="mb-0 fs-2">Online</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex align-items-center">
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ri-phone-line"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ri-vidicon-line"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-dark px-2 fs-7 nav-icon-hover" href="#">
                                    <i class="ri-more-2-line"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Messages container -->
                    <div class="chat-box h-100">
                        <div class="chat-box-inner p-3" id="messages-container" data-simplebar>
                            <!-- Messages will be loaded here -->
                        </div>
                        
                        <!-- Message input area -->
                        <div class="px-3 py-3 border-top chat-send-message-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 w-85">
                                    <button id="attach-file" class="btn btn-sm text-dark">
                                        <i class="ri-attachment-line"></i>
                                    </button>
                                    <input type="file" id="file-input" class="d-none">
                                    <input type="text" id="message-input" class="form-control message-type-box border-0 p-2" placeholder="Type a Message">
                                    <button class="btn btn-sm text-dark">
                                        <i class="ri-emotion-happy-line"></i>
                                    </button>
                                </div>
                                <button id="send-message" class="btn btn-primary">
                                    <i class="ri-send-plane-2-line"></i>
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
<div class="offcanvas offcanvas-start user-chat-box" tabindex="-1" id="mobile-sidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Chats</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Mobile user list content will be cloned from desktop sidebar -->
    </div>
</div>

<!-- Audio element for notification sound -->
<audio id="notification-sound" src="/path/to/notification.mp3" preload="auto"></audio>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let activeChat = null;
    let users = [];
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
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileSidebar = new bootstrap.Offcanvas(document.getElementById('mobile-sidebar'));
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

    mobileMenuButton.addEventListener('click', function() {
        mobileSidebar.show();
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
    }

    function setupEventListeners() {
        sendMessageButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
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
                            <img src="${user.avatar}" alt="${user.name}" width="48" height="48" class="rounded-circle">
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
    }

    // Open chat with a user
    window.openChat = function(userId) {
        activeChat = userId;
        const selectedUser = users.find(user => user.id == userId);

        // Update active chat header
        document.getElementById('active-chat-avatar').src = selectedUser.avatar;
        document.getElementById('active-chat-name').textContent = `${selectedUser.name} ${selectedUser.last}`;
        document.getElementById('active-chat-status').className = `position-absolute bottom-0 end-0 p-1 badge rounded-pill ${selectedUser.online ? 'bg-success' : 'bg-secondary'}`;
        document.getElementById('active-chat-status-text').textContent = selectedUser.online ? 'Online' : 'Offline';

        // Switch views
        noChatSelected.classList.add('d-none');
        activeChatArea.classList.remove('d-none');
        
        // On mobile, hide user list and show chat
        if (window.innerWidth < 992) {
            userListContainer.classList.remove('d-block');
            chatContainer.classList.remove('d-none');
            mobileSidebar.hide();
        }

        loadConversation(userId);
        clearUnreadMessages(userId);
    };

    // Show no chat selected view
    function showNoChatSelected() {
        noChatSelected.classList.remove('d-none');
        activeChatArea.classList.add('d-none');
    }

    // Load conversation
    function loadConversation(userId) {
        messagesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Loading messages...</div>';

        fetch(`/chat/conversations/${userId}`)
            .then(response => response.json())
            .then(messages => {
                if (messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No messages yet. Start a conversation!</div>';
                } else {
                    renderMessages(messages);
                }
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading conversation:', error);
                messagesContainer.innerHTML = `<div class="p-4 text-center text-gray-500">Error loading messages: ${error.message}</div>`;
            });
    }

    // Render messages
    function renderMessages(messages) {
        messagesContainer.innerHTML = messages.map(msg => `
            <div class="flex flex-col mb-4 ${msg.sender === 'agent' ? 'items-end' : 'items-start'}"
                 data-message-id="${msg.id}"
                 data-status="${msg.status || 'sent'}">
                <div class="p-3 rounded-3xl inline-flex max-w-[80%]
                    ${msg.sender === 'agent' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800'}
                    break-all overflow-hidden">
                    ${msg.file_path ?
                        (msg.file_path.match(/\.(png|jpg|jpeg|gif)$/) ?
                            `<div class="image-container"><img src="${msg.file_path}" alt="File" class="chat-image" onclick="openImagePopup('${msg.file_path}')"></div>` :
                            `<a href="/storage/${msg.file_path}" download>Download File</a>`
                        ) : msg.message
                    }
                </div>
                <div class="message-status text-xs ${msg.sender === 'agent' ? 'text-right' : 'text-left'}">
                    ${formatMessageTime(msg.timestamp)}
                    ${msg.sender === 'agent' ? getStatusIndicator(msg.status, msg.timestamp) : ''}
                </div>
            </div>
        `).join('');
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
            return msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (timeDiff < oneWeek) {
            return msgDate.toLocaleDateString([], { weekday: 'short' }) + ' ' +
                   msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else {
            return msgDate.toLocaleDateString([], { month: 'short', day: 'numeric' }) + ' ' +
                   msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }

    // Get status indicator
    function getStatusIndicator(status, timestamp = new Date()) {
        if (!status) return '';
        status = String(status).trim().toLowerCase();
        
        const formattedTime = formatMessageTime(timestamp);
        switch (status) {
            case 'sending': return `<span class="status-indicator">Sending...</span>`;
            case 'sent': return `<span class="status-indicator">Sent</span>`;
            case 'delivered': return `<span class="status-indicator">Delivered</span>`;
            case 'seen': return `<span class="status-indicator text-success">Seen</span>`;
            default: return '';
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
        tempMessageElement.className = 'flex flex-col mb-4 items-end';
        tempMessageElement.setAttribute('data-message-id', tempMsgId);
        tempMessageElement.innerHTML = `
            <div class="p-3 rounded-3xl inline-flex max-w-[80%] bg-primary text-white break-all overflow-hidden">
                ${message}
            </div>
            <div class="message-status text-xs text-gray-400 text-right">
                ${getStatusIndicator('sending', now)}
            </div>
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
        const formData = new FormData();
        formData.append('to_user_id', activeChat);
        formData.append('file', file);

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tempMsgId = 'temp-' + Date.now();
            const now = new Date();

            const tempMessageElement = document.createElement('div');
            tempMessageElement.className = 'flex flex-col mb-4 items-end';
            tempMessageElement.setAttribute('data-message-id', tempMsgId);
            tempMessageElement.innerHTML = `
                <div class="p-3 rounded-3xl inline-flex max-w-[80%] bg-primary text-white break-all overflow-hidden">
                    ${file.type.startsWith('image/') ?
                        `<img src="${URL.createObjectURL(file)}" alt="Sent Image" class="chat-image" onclick="openImagePopup('${URL.createObjectURL(file)}')">` :
                        `<a href="/storage/${data.file_path}" download="${file.name}">Download ${file.name}</a>`
                    }
                </div>
                <div class="message-status text-xs text-gray-400 text-right">
                    ${getStatusIndicator('sending', now)}
                </div>
            `;
            messagesContainer.appendChild(tempMessageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            updateMessageStatus(data.id, 'sent', data.timestamp);
            updateUserLastMessage(activeChat, file.type.startsWith('image/') ? '[Image]' : `[File: ${file.name}]`, data.timestamp);

            if (users.find(user => user.id == activeChat).online) {
                markMessageAsDelivered(data.id);
                updateMessageStatus(data.id, 'delivered', data.timestamp);
            }
        })
        .catch(error => {
            console.error('Error sending message with file:', error);
        });
    }

    // Update message status
    function updateMessageStatus(messageId, status, timestamp = new Date()) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            const statusElement = messageElement.querySelector('.message-status');
            if (statusElement) {
                const timeElement = statusElement.querySelector('.time') || document.createElement('span');
                timeElement.className = 'time';
                timeElement.textContent = formatMessageTime(timestamp);
                
                const statusIndicator = getStatusIndicator(status, timestamp);
                statusElement.innerHTML = `${timeElement.outerHTML} ${statusIndicator}`;
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
        if (userIndex !== -1) {
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
                    <i class="ri-close-line"></i>
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

    // Initialize the chat
    initializeChat();
});
</script>
</body>
</html>
@endsection