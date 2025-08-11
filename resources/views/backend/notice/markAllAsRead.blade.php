<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <title>Notifications</title>
</head>
<body class="bg-gray-100 min-h-screen p-4">
    <div class="notifications-panel">
        <!-- Header with Mark All as Read button -->
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-semibold">Notifications</h2>
            @if($unreadCount > 0)
                <button 
                    id="markAllReadBtn"
                    class="text-sm text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="notifications-list" id="notificationsList">
            @forelse($notifications as $notification)
                <div class="notification-item p-4 border-b hover:bg-gray-50 {{ $notification['read'] ? 'bg-gray-50' : 'bg-white' }}"
                     data-notification-id="{{ $notification['id'] }}">
                    <div class="flex items-start">
                        @if(!$notification['read'])
                            <span class="h-2 w-2 mt-2 mr-2 bg-blue-600 rounded-full unread-indicator"></span>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $notification['message'] }}</p>
                            <p class="text-sm text-gray-600">{{ $notification['content'] }}</p>
                            <span class="text-xs text-gray-500">{{ $notification['time'] }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No notifications
                </div>
            @endforelse
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
        <div class="bg-white p-4 rounded-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-center mt-2">Processing...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', markAllAsRead);
            }

            function showLoading() {
                loadingOverlay.classList.remove('hidden');
                loadingOverlay.classList.add('flex');
            }

            function hideLoading() {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
            }

            async function markAllAsRead() {
                try {
                    showLoading();
                    
                    const response = await fetch('/notice/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        // Update UI
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.add('bg-gray-50');
                            const indicator = item.querySelector('.unread-indicator');
                            if (indicator) {
                                indicator.remove();
                            }
                        });

                        // Hide the mark all read button
                        markAllReadBtn.style.display = 'none';

                        // Optional: Show success message
                        showNotification('All notifications marked as read', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to mark notifications as read');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Failed to mark notifications as read', 'error');
                } finally {
                    hideLoading();
                }
            }

            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed bottom-4 right-4 p-4 rounded-lg text-white ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } transition-opacity duration-500`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>

    <style>
        .notifications-panel {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .notifications-list {
            max-height: 500px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .notifications-list::-webkit-scrollbar {
            width: 6px;
        }

        .notifications-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .notifications-list::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .notification-item {
            transition: all 0.2s ease-in-out;
        }

        .notification-item:hover {
            transform: translateX(2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</body>
</html>