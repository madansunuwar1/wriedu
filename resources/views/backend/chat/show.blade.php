<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Render the ChatWidget component -->
        <x-chat-widget />
    </div>

    <!-- Include Laravel Echo and Pusher for real-time messaging -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Initialize Laravel Echo
        const echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true,
        });

        // Listen for the NewMessage event
        echo.private(`chat.{{ $user->id }}`)
            .listen('NewMessage', (e) => {
                // Append the new message to the chat messages container
                const chatMessages = document.getElementById('chat-messages');
                const messageElement = document.createElement('div');
                messageElement.className = 'mb-2';
                messageElement.innerHTML = `
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <p class="text-sm text-gray-700">${e.message.message}</p>
                        <p class="text-xs text-gray-500">${e.message.timestamp}</p>
                    </div>
                `;
                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the bottom
            });

        // Handle message submission
        document.getElementById('send-message-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();

            if (message) {
                // Send the message via AJAX
                fetch('/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        to_user_id: {{ $user->id }},
                        message: message,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    // Clear the input field
                    messageInput.value = '';
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>
</html>