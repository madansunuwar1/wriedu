<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>WRI Education Consultancy</title>
    <link rel="icon" type="image/png" href="{{ asset('img/wri.png') }}">
</head>
<body>
    <script>
        // In your JavaScript file
window.Echo.private(`notifications.${userId}`)
    .listen('.user.mentioned', (e) => {
        // Create notification
        const notification = {
            message: `${e.mentioner_name} mentioned you in a comment`,
            time: e.time,
            comment_id: e.comment_id,
            comment_text: e.comment_text
        };

        // Show notification
        showNotification(notification);

        // Update notification count
        updateNotificationCount();
    });

function showNotification(notification) {
    // Create notification element
    const notificationElement = document.createElement('div');
    notificationElement.className = 'notification show';
    notificationElement.innerHTML = `
        <div class="notification-content">
            <p class="notification-message">${notification.message}</p>
            <p class="notification-text">${notification.comment_text}</p>
            <span class="notification-time">${formatTime(notification.time)}</span>
        </div>
    `;

    // Add to notification container
    document.querySelector('.notification-container').prepend(notificationElement);

    // Remove after 5 seconds
    setTimeout(() => {
        notificationElement.remove();
    }, 5000);
}

function updateNotificationCount() {
    const countElement = document.querySelector('.notification-count');
    if (countElement) {
        const currentCount = parseInt(countElement.textContent) || 0;
        countElement.textContent = currentCount + 1;
    }
}

function formatTime(timeString) {
    const date = new Date(timeString);
    return date.toLocaleTimeString();
}
    </script>
</body>
</html>