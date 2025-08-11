<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<style>
.notification-badge {
    position: absolute;
    top: 10px;
    right: -4px;
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    font-size: 10px;
    min-width: 16px;
    height: 16px;
    line-height: 16px;
    text-align: center;
    display: none;
}

.notification-badge.has-notifications {
    display: inline-block;
}

.notification-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f1f9ff;
}

.notification-icon {
    margin-right: 12px;
    width: 32px;
    height: 32px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.notification-item.unread .notification-icon {
    background-color: #2e86de;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationCount = document.getElementById('notificationCount');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    
    // Show/hide dropdown
    notificationDropdown.addEventListener('click', function(e) {
        const dropdownMenu = this.nextElementSibling;
        dropdownMenu.classList.toggle('show');
        
        // If showing dropdown, fetch notifications
        if (dropdownMenu.classList.contains('show')) {
            fetchNotifications();
        }
    });
    
    
    // Function to fetch notifications
    function fetchNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                renderNotifications(data);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }
    
    // Function to fetch unread count
    function fetchUnreadCount() {
        fetch('/notifications/unread')
            .then(response => response.json())
            .then(data => {
                const unreadCount = data.length;
                if (unreadCount > 0) {
                    notificationCount.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    notificationCount.classList.add('has-notifications');
                } else {
                    notificationCount.textContent = '0';
                    notificationCount.classList.remove('has-notifications');
                }
            })
            .catch(error => {
                console.error('Error fetching unread count:', error);
            });
    }
    
    // Function to render notifications
    function renderNotifications(notifications) {
        // Clear existing notifications
        notificationList.innerHTML = '';
        
        // If no notifications, show message
        if (notifications.length === 0) {
            const noNotifications = document.createElement('div');
            noNotifications.className = 'text-center py-3 text-muted';
            noNotifications.textContent = 'No notifications';
            notificationList.appendChild(noNotifications);
            return;
        }
        
        // Render each notification
        notifications.forEach(notification => {
            const notificationItem = document.createElement('a');
            notificationItem.href = 'javascript:void(0)';
            notificationItem.className = `dropdown-item px-7 d-flex align-items-center py-6 notification-item ${!notification.read ? 'unread' : ''}`;
            notificationItem.dataset.id = notification.id;
            
            // Get icon based on type
            let iconClass = 'solar:bell-line-duotone';
            switch (notification.type) {
                case 'comment': iconClass = 'solar:chat-line-duotone'; break;
                case 'mention': iconClass = 'solar:at-line-duotone'; break;
                case 'notice': iconClass = 'solar:megaphone-line-duotone'; break;
                case 'reminder': iconClass = 'solar:clock-line-duotone'; break;
            }
            
            notificationItem.innerHTML = `
                <span class="flex-shrink-0 notification-icon">
                    <iconify-icon icon="${iconClass}" class="fs-5"></iconify-icon>
                </span>
                <div class="w-100 ps-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fs-3 fw-normal">${notification.title || 'Notification'}</h5>
                        <span class="fs-2 text-nowrap d-block text-muted">${notification.created_at}</span>
                    </div>
                    <span class="fs-2 d-block mt-1 text-muted">${notification.message}</span>
                </div>
            `;
            
            // Add click handler
            notificationItem.addEventListener('click', function() {
                // Mark as read
                markAsRead(notification.id);
                
                // Navigate if there's a link
                if (notification.link) {
                    window.location.href = notification.link;
                }
            });
            
            notificationList.appendChild(notificationItem);
        });
    }
    
    // Function to mark notification as read
    function markAsRead(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification in the list
                const notificationItem = notificationList.querySelector(`[data-id="${id}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.add('read');
                }
                
                // Update notification count
                fetchUnreadCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Function to mark all as read
    function markAllAsRead() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                fetchNotifications();
                fetchUnreadCount();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
    
    // Mark all read button
    markAllReadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });
    
    // Initial fetch of unread count
    fetchUnreadCount();
    
    // Set up polling every 30 seconds
    setInterval(fetchUnreadCount, 30000);
    
    // Make updateNotifications available globally for real-time updates
    window.updateNotifications = function() {
        fetchUnreadCount();
        const dropdownMenu = document.querySelector('.dropdown-menu.show');
        if (dropdownMenu) {
            fetchNotifications();
        }
    };
});
</script>
</body>
</html>