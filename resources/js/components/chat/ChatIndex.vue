<template>
    <div class="card overflow-hidden chat-application m-4">
        <div class="d-flex h-100">
            <!-- Left Sidebar - User List -->
            <div class="w-30 d-none d-lg-block border-end user-chat-box" id="user-list-container">
                <div v-if="currentUser" class="px-4 pt-9 pb-6">
                    <!-- Current User Info -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="position-relative">
                                <img :src="currentUser.avatar_url" :alt="currentUser.name" width="54" height="54"
                                    class="rounded-circle">
                                <span class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success"><span
                                        class="visually-hidden">Online</span></span>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-2">{{ currentUser.name }} {{ currentUser.last }}</h6>
                                <p class="mb-0 fs-2">
                                    <span :class="connectionStatus === 'connected' ? 'text-success' : 'text-warning'">
                                        {{ connectionStatus === 'connected' ? 'Online' : 'Connecting...' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Search -->
                    <form class="position-relative mb-4">
                        <input type="text" v-model="searchQuery" class="form-control search-chat py-2 ps-5"
                            placeholder="Search Contact">
                        <i
                            class="ri-search-line position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </form>
                </div>

                <!-- User List -->
                <div class="app-chat h-100">
                    <ul class="chat-users mh-n100" data-simplebar>
                        <li v-for="user in filteredUsers" :key="user.id" @click="openChat(user)">
                            <a href="#" class="px-4 py-3 d-flex align-items-start justify-content-between chat-user"
                                :class="{ 'bg-light-subtle': activeChatUser && activeChatUser.id === user.id }">
                                <div class="d-flex align-items-center">
                                    <span class="position-relative">
                                        <img :src="user.avatar" :alt="user.name" width="48" height="48"
                                            class="rounded-circle">
                                        <span v-if="user.online"
                                            class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-success"></span>
                                        <span v-else
                                            class="position-absolute bottom-0 end-0 p-1 badge rounded-pill bg-secondary"></span>
                                    </span>
                                    <div class="ms-3 d-inline-block w-75">
                                        <h6 class="mb-1 fw-semibold" :class="{ 'text-dark': user.unread > 0 }">{{
                                            user.name }} {{ user.last }}</h6>
                                        <span class="fs-3 text-truncate d-block"
                                            :class="{ 'text-dark fw-semibold': user.unread > 0, 'text-body-color': !user.unread }">
                                            {{ user.lastMessage || 'No messages yet' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <p class="fs-2 mb-0 text-muted">{{ formatTime(user.lastMessageTime) }}</p>
                                    <span v-if="user.unread > 0" class="badge bg-danger rounded-pill">{{ user.unread
                                    }}</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side - Chat Area -->
            <div class="w-70 w-xs-100 chat-container">
                <div class="chat-box-inner-part h-100">
                    <!-- Default state when no chat is selected -->
                    <div v-if="!activeChatUser" class="chat-not-selected h-100">
                        <div class="d-flex align-items-center justify-content-center h-100 p-5">
                            <div class="text-center">
                                <span class="text-primary"><i class="ri-message-2-line fs-10"></i></span>
                                <h6 class="mt-2">Select a chat to start messaging</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Active chat area -->
                    <div v-else class="chatting-box d-flex flex-column h-100">
                        <!-- Chat Header -->
                        <div class="p-3 border-bottom chat-meta-user d-flex align-items-center justify-content-between">
                            <div class="hstack gap-3 current-chat-user-name">
                                <div class="position-relative">
                                    <img :src="activeChatUser.avatar" :alt="activeChatUser.name" width="48" height="48"
                                        class="rounded-circle">
                                    <span :class="activeChatUser.online ? 'bg-success' : 'bg-secondary'"
                                        class="position-absolute bottom-0 end-0 p-1 badge rounded-pill"></span>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ activeChatUser.name }} {{ activeChatUser.last }}
                                    </h6>
                                    <p v-if="typingUser === activeChatUser.id" class="mb-0 fs-2 text-primary">typing...
                                    </p>
                                    <p v-else class="mb-0 fs-2">{{ activeChatUser.online ? 'Online' :
                                        getLastSeen(activeChatUser.id) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div class="chat-box-inner flex-grow-1 p-3" ref="messagesContainer" @scroll="handleScroll">
                            <!-- Loading indicator -->
                            <div v-if="loadingMessages" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <div v-for="message in messages" :key="message.id" class="d-flex mb-2"
                                :class="message.from_user_id === currentUser.id ? 'justify-content-end' : 'justify-content-start'">
                                <div class="card mw-80"
                                    :class="message.from_user_id === currentUser.id ? 'bg-primary text-white' : 'bg-light'">
                                    <div class="card-body p-2">
                                        <!-- File Message -->
                                        <div v-if="message.file_path || message.temp_file_name">
                                            <!-- Image preview (works for both real and optimistic messages) -->
                                            <a v-if="isImage(message.file_path || message.temp_file_name)"
                                                :href="message.file_path ? `/storage/${message.file_path}` : '#'"
                                                target="_blank">
                                                <img :src="message.file_path && !message.file_path.startsWith('blob:') ? `/storage/${message.file_path}` : message.file_path"
                                                    class="chat-image rounded" style="max-height: 150px;" alt="image">
                                            </a>
                                            <!-- File link -->
                                            <a v-else :href="message.file_path ? `/storage/${message.file_path}` : '#'"
                                                download class="d-flex align-items-center text-decoration-none"
                                                :class="message.from_user_id === currentUser.id ? 'text-white' : 'text-dark'">
                                                <i class="ri-file-line fs-5 me-2"></i> {{ getFileName(message.file_path)
                                                || message.temp_file_name }}
                                            </a>
                                        </div>
                                        <!-- Text Message -->
                                        <p v-if="message.message" class="mb-0 fs-3">{{ message.message }}</p>
                                        <div class="d-flex align-items-center justify-content-end mt-1">
                                            <p class="fs-2 mb-0 me-2 text-muted">{{ formatTime(message.created_at) }}
                                            </p>
                                            <i v-if="message.from_user_id === currentUser.id"
                                                :class="getStatusIcon(message.status)" class="fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input Area -->
                        <div class="px-3 py-3 border-top chat-send-message-footer">
                            <form @submit.prevent="sendMessage"
                                class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 w-100">
                                    <label for="file-input" class="btn btn-sm text-dark mb-0">
                                        <i class="ri-attachment-line"></i>
                                    </label>
                                    <input type="file" id="file-input" @change="handleFileSelect" class="d-none">
                                    <input type="text" v-model="newMessage" @input="handleTyping"
                                        @keydown.enter.prevent="sendMessage"
                                        class="form-control message-type-box border-0 p-2"
                                        placeholder="Type a Message..." :disabled="sendingMessage">
                                </div>
                                <button type="submit" class="btn btn-primary ms-2"
                                    :disabled="sendingMessage || (!newMessage.trim() && !newFile)">
                                    <i v-if="sendingMessage" class="ri-loader-4-line"></i>
                                    <i v-else class="ri-send-plane-2-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { format, parseISO, formatDistanceToNow } from 'date-fns';

// State
const currentUser = ref(null);
const users = ref([]);
const activeChatUser = ref(null);
const messages = ref([]);
const newMessage = ref('');
const newFile = ref(null);
const searchQuery = ref('');
const typingUser = ref(null);
const connectionStatus = ref('connecting');
const loadingMessages = ref(false);
const sendingMessage = ref(false);
const lastSeenData = ref({});

let typingTimer = null;
let typingIndicatorTimer = null;
const messagesContainer = ref(null);

// Initialize notification sound
const notificationSound = new Audio('/sounds/notification.mp3');
notificationSound.volume = 0.5;

// Computed Properties
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value.sort((a, b) => {
        // Sort by last message time, descending
        const aTime = a.lastMessageTime ? new Date(a.lastMessageTime).getTime() : 0;
        const bTime = b.lastMessageTime ? new Date(b.lastMessageTime).getTime() : 0;
        return bTime - aTime;
    });
    return users.value.filter(user =>
        `${user.name} ${user.last}`.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});


// Helper Functions
const formatTime = (isoString) => {
    if (!isoString) return '';
    try {
        const date = parseISO(isoString);
        const now = new Date();
        const messageDate = new Date(date);

        if (now.toDateString() === messageDate.toDateString()) {
            return format(date, 'p'); // '4:30 PM'
        }
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (yesterday.toDateString() === messageDate.toDateString()) {
            return 'Yesterday';
        }
        return format(date, 'MMM dd'); // 'Jan 23'
    } catch (error) {
        console.error("Error formatting time:", isoString, error);
        return '';
    }
};

const getLastSeen = (userId) => {
    if (lastSeenData.value[userId]) {
        return `Last seen ${formatDistanceToNow(parseISO(lastSeenData.value[userId]), { addSuffix: true })}`;
    }
    return 'Offline';
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'seen': return 'ri-check-double-line text-info';
        case 'delivered': return 'ri-check-double-line';
        case 'sent': return 'ri-check-line';
        case 'sending': return 'ri-time-line text-muted'; // For optimistic UI
        case 'failed': return 'ri-error-warning-line text-danger'; // For failed messages
        default: return 'ri-time-line text-muted';
    }
};

const isImage = (filePath) => filePath && /\.(jpe?g|png|gif|webp)$/i.test(filePath);
const getFileName = (filePath) => filePath ? filePath.split('/').pop() : '';

// Methods
const scrollToBottom = async (smooth = true) => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }
};

const playNotificationSound = () => {
    notificationSound.play().catch(e => console.log('Could not play notification sound:', e));
};

const fetchCurrentUser = async () => {
    try {
        const response = await axios.get('/api/chat/current-user');
        currentUser.value = response.data;
        return response.data;
    } catch (error) {
        console.error("Error fetching current user (user may not be logged in):", error);
        return null;
    }
};

const fetchUsers = async () => {
    try {
        const response = await axios.get('/api/chat/users');
        const fetchedUsers = response.data;

        // Preserve existing last message info if not present in new data
        fetchedUsers.forEach(newUser => {
            const existingUser = users.value.find(u => u.id === newUser.id);
            if (existingUser) {
                newUser.lastMessage = newUser.lastMessage || existingUser.lastMessage;
                newUser.lastMessageTime = newUser.lastMessageTime || existingUser.lastMessageTime;
                newUser.unread = newUser.unread || existingUser.unread;
            }
        });

        users.value = fetchedUsers;

        if (activeChatUser.value) {
            const updatedUser = users.value.find(u => u.id === activeChatUser.value.id);
            if (updatedUser) {
                activeChatUser.value.online = updatedUser.online;
            }
        }
    } catch (error) {
        console.error("Error fetching users:", error);
    }
};

const openChat = async (user) => {
    if (activeChatUser.value?.id === user.id) return;

    loadingMessages.value = true;
    activeChatUser.value = user;
    messages.value = [];

    try {
        const response = await axios.get(`/api/chat/conversations/${user.id}`);
        messages.value = response.data; // Assuming messages come newest first
        scrollToBottom(false);

        if (user.unread > 0) {
            await markMessagesAsSeen(user.id);
            const userInList = users.value.find(u => u.id === user.id);
            if (userInList) {
                userInList.unread = 0;
            }
        }
    } catch (error) {
        console.error("Error fetching conversation:", error);
    } finally {
        loadingMessages.value = false;
    }
};

const handleFileSelect = (event) => {
    newFile.value = event.target.files[0];
    if (newFile.value) {
        sendMessage();
    }
};

const sendMessage = async () => {
    // Basic validation
    if ((!newMessage.value.trim() && !newFile.value) || sendingMessage.value || !activeChatUser.value) {
        return;
    }

    sendingMessage.value = true;

    // --- Start of Optimistic UI ---

    // 1. Create a temporary message object to display instantly.
    const tempId = `temp-${Date.now()}`;
    const optimisticMessage = {
        id: tempId,
        from_user_id: currentUser.value.id,
        to_user_id: activeChatUser.value.id,
        message: newMessage.value.trim(),
        // Create a temporary local URL for image previews
        file_path: newFile.value && isImage(newFile.value.name) ? URL.createObjectURL(newFile.value) : null,
        // Show a placeholder for non-image files
        temp_file_name: newFile.value ? getFileName(newFile.value.name) : null,
        status: 'sending', // Show a clock icon
        created_at: new Date().toISOString(),
    };

    // 2. Add the optimistic message to the array. Vue's reactivity handles the rest.
    messages.value.push(optimisticMessage);
    await scrollToBottom();

    // 3. Prepare the form data and clear the inputs *after* creating the temp message.
    const formData = new FormData();
    formData.append('to_user_id', activeChatUser.value.id);
    if (newMessage.value.trim()) {
        formData.append('message', newMessage.value.trim());
    }
    if (newFile.value) {
        formData.append('file', newFile.value);
    }

    // Clear inputs for the next message
    newMessage.value = '';
    newFile.value = null;
    document.getElementById('file-input').value = '';

    // --- End of Optimistic UI ---

    try {
        // 4. Send the actual request to the server.
        const response = await axios.post('/api/chat/send', formData);
        const savedMessage = response.data;

        // 5. On success, find our temporary message and replace it with the real one from the server.
        const index = messages.value.findIndex(m => m.id === tempId);
        if (index !== -1) {
            // Revoke the blob URL to prevent memory leaks if it was an image
            if (optimisticMessage.file_path) {
                URL.revokeObjectURL(optimisticMessage.file_path);
            }
            messages.value.splice(index, 1, savedMessage);
        }

        // Also update the user list on the left
        updateUserListOnNewMessage(savedMessage);

    } catch (error) {
        console.error("Error sending message:", error);

        // 6. On failure, find the temporary message and update its status to 'failed'.
        const index = messages.value.findIndex(m => m.id === tempId);
        if (index !== -1) {
            messages.value[index].status = 'failed';
        }
        // Optionally, show an alert to the user.
        alert('Failed to send message. Please check your connection and try again.');
    } finally {
        sendingMessage.value = false;
    }
};


const handleTyping = () => {
    if (activeChatUser.value && window.Echo) {
        try {
            window.Echo.private(`chat.${activeChatUser.value.id}`)
                .whisper('typing', { from: currentUser.value.id });
        } catch (e) {
            console.warn("Could not send typing event. Echo might not be connected.", e);
        }
    }
};

const markMessagesAsSeen = async (fromUserId) => {
    try {
        await axios.post('/api/chat/mark-seen', { from_user_id: fromUserId });
    } catch (error) {
        console.error("Error marking messages as seen:", error);
    }
};

const updateUserListOnNewMessage = (message) => {
    const isMyMessage = message.from_user_id === currentUser.value.id;
    const otherUserId = isMyMessage ? message.to_user_id : message.from_user_id;

    let userInList = users.value.find(u => u.id === otherUserId);
    if (userInList) {
        userInList.lastMessage = message.file_path ? '[File]' : (message.message || '');
        userInList.lastMessageTime = message.created_at;

        if (!isMyMessage && (!activeChatUser.value || activeChatUser.value.id !== otherUserId)) {
            userInList.unread = (userInList.unread || 0) + 1;
        }
    }
};

const handleNewMessage = (message) => {
    updateUserListOnNewMessage(message);

    // If chat with sender is open, add message to view
    if (activeChatUser.value && activeChatUser.value.id === message.from_user_id) {
        messages.value.push(message);
        scrollToBottom();
        markMessagesAsSeen(message.from_user_id);
    } else {
        playNotificationSound(); // Play sound only for background notifications
    }
};

const updateMessageStatus = (messageId, status) => {
    const message = messages.value.find(m => m.id === messageId);
    if (message) {
        message.status = status;
    }
};

const handleTypingIndicator = (userId) => {
    if (activeChatUser.value && activeChatUser.value.id === userId) {
        typingUser.value = userId;
        if (typingTimer) clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            typingUser.value = null;
        }, 3000);
    }
};

const updateUserOnlineStatus = (userId, isOnline) => {
    const user = users.value.find(u => u.id === userId);
    if (user) {
        user.online = isOnline;
        if (!isOnline) {
            lastSeenData.value[userId] = new Date().toISOString();
        }
    }
    if (activeChatUser.value && activeChatUser.value.id === userId) {
        activeChatUser.value.online = isOnline;
    }
};

const setupEchoListeners = () => {
    if (!currentUser.value) {
        console.error("Cannot setup Echo listeners without a current user.");
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('❌ CSRF token not found. Make sure <meta name="csrf-token" ...> is in your main layout file.');
        connectionStatus.value = 'error';
        return;
    }

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                // For session-based auth, only the CSRF token is needed.
                // The browser will automatically send the session cookie.
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        }
    });

    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        console.log(`Pusher connection state: ${states.previous} -> ${states.current}`);
        connectionStatus.value = states.current;
    });

    // Listen for new messages on the user's private channel
    window.Echo.private(`chat.${currentUser.value.id}`)
        .listen('NewMessage', (e) => handleNewMessage(e.message))
        .listen('MessageStatusUpdated', (e) => updateMessageStatus(e.message_id, e.status))
        .listenForWhisper('typing', (e) => handleTypingIndicator(e.from));

    // Listen for user presence (online/offline status)
    window.Echo.join('presence-chat')
        .here((onlineUsers) => {
            console.log('Online users:', onlineUsers.map(u => u.name));
            const onlineIds = onlineUsers.map(u => u.id);
            users.value.forEach(u => u.online = onlineIds.includes(u.id));
        })
        .joining((user) => {
            console.log(`${user.name} has joined.`);
            updateUserOnlineStatus(user.id, true);
        })
        .leaving((user) => {
            console.log(`${user.name} has left.`);
            updateUserOnlineStatus(user.id, false);
        });
};

onMounted(async () => {
    console.log('🚀 Component mounted, initializing...');

    const user = await fetchCurrentUser();
    if (!user) {
        console.error("Initialization failed: User is not logged in. Redirecting or showing login message might be needed.");
        connectionStatus.value = 'auth_error';
        // Optionally, redirect to login page: window.location.href = '/login';
        return;
    }

    console.log(`✅ Logged in as: ${user.name}`);
    await fetchUsers();
    setupEchoListeners();
});

onUnmounted(() => {
    if (window.Echo) {
        console.log("🔌 Disconnecting from Echo channels.");
        window.Echo.leave('presence-chat');
        if (currentUser.value) {
            window.Echo.leave(`chat.${currentUser.value.id}`);
        }
        window.Echo.disconnect();
    }
    if (typingTimer) clearTimeout(typingTimer);
    if (typingIndicatorTimer) clearTimeout(typingIndicatorTimer);
});
</script>

<style scoped>
/* Base styles from original widget */
.chat-application {
    height: calc(100vh - 2rem);
}

.user-chat-box {
    width: 30%;
    border-right: 1px solid #e5e7eb;
}

.chat-container {
    width: 70%;
}

.chat-box-inner {
    overflow-y: auto;
}

.chat-send-message-footer {
    border-top: 1px solid #e5e7eb;
}

.chat-users {
    height: 100%;
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.chat-user {
    text-decoration: none;
    display: block;
    color: inherit;
    transition: background-color 0.2s ease-in-out;
    cursor: pointer;
}

.chat-user:hover,
.chat-user.bg-light-subtle {
    background-color: #f8f9fa;
}

.mw-80 {
    max-width: 80%;
}

.chatting-box {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.chat-box-inner {
    flex-grow: 1;
    overflow-y: auto;
    scroll-behavior: smooth;
}

/* Animation for new messages */
@keyframes slideInMessage {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: slideInMessage 0.3s ease-out;
}

/* Typing indicator */
.typing-indicator {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        opacity: 0.6;
    }

    50% {
        opacity: 1;
    }

    100% {
        opacity: 0.6;
    }
}

/* Loading spinner animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.ri-loader-4-line {
    animation: spin 1s linear infinite;
}

/* Connection status indicator */
.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 6px;
}

::-webkit-scrollbar-thumb:hover {
    background: #aaa;
}

/* Mobile responsiveness */
@media (max-width: 991px) {
    .user-chat-box {
        display: none !important;
    }

    .chat-container {
        width: 100% !important;
    }
}
</style>