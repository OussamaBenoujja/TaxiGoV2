@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-blue-600 dark:bg-gray-700 text-white px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold">Chat with {{ $otherUser->name }}</h1>
                <p class="text-sm text-gray-100">
                    Booking #{{ $booking->id }} - 
                    {{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="bg-blue-700 hover:bg-blue-800 dark:bg-gray-600 dark:hover:bg-gray-500 text-white py-2 px-4 rounded">
                Back
            </a>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" class="p-4 h-96 overflow-y-auto dark:bg-gray-900">
            @foreach($messages as $message)
                <div class="mb-4 {{ $message->sender_id == Auth::id() ? 'text-right' : 'text-left' }}">
                    <div class="inline-block rounded-lg px-4 py-2 max-w-xs lg:max-w-md {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white' }}">
                        <p class="font-semibold text-sm">
                            {{ $message->sender_id == Auth::id() ? 'You' : $message->sender->name }}
                        </p>
                        <p>{{ $message->message }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id == Auth::id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $message->created_at->format('h:i A') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Chat Input -->
        <div class="border-t dark:border-gray-700 p-4 dark:bg-gray-800">
        <form id="message-form" class="flex" onsubmit="return false;">
    @csrf
    <input 
        type="text" 
        id="message-input" 
        name="message" 
        class="flex-1 border dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-l-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-300" 
        placeholder="Type your message..."
        required
    >
    <button 
        type="button" 
        id="send-message-btn"
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-r-lg"
    >
        Send
    </button>
</form>
        </div>
    </div>
</div>
<!-- Add this right after the chat container -->
<div class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
    <h3 class="font-medium mb-2">Debug Info:</h3>
    <p>Booking ID: {{ $booking->id }}</p>
    <p>Your ID: {{ Auth::id() }}</p>
    <p>Other User ID: {{ $otherUser->id }}</p>
    <p>Channel: chat.{{ $booking->id }}</p>
    <p>CSRF Token Present: {{ csrf_token() ? 'Yes' : 'No' }}</p>
    <button onclick="testEcho()" class="mt-2 bg-gray-300 dark:bg-gray-700 px-3 py-1 rounded">Test Echo</button>
</div>

<script>
function testEcho() {
    console.log('Testing Echo configuration...');
    
    if (!window.Echo) {
        console.error('Echo not defined');
        alert('Echo is not defined. Check your bootstrap.js file.');
        return;
    }
    
    console.log('Echo object:', window.Echo);
    console.log('Pusher object:', window.Pusher);
    
    if (window.Echo.connector && window.Echo.connector.pusher) {
        console.log('Pusher connection state:', window.Echo.connector.pusher.connection.state);
    }
    
    alert('Echo debug info logged to console. Please check browser developer tools.');
}
</script>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded event fired');
        
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-message-btn');
        const chatMessages = document.getElementById('chat-messages');
        
        console.log('Elements found:', {
            messageForm: !!messageForm,
            messageInput: !!messageInput, 
            sendButton: !!sendButton,
            chatMessages: !!chatMessages
        });
        
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Scroll to bottom on page load
        scrollToBottom();
        
        // Check if Echo is defined
        console.log('Echo available:', !!window.Echo);
        
        // Initialize Echo and listen for messages
        if (window.Echo) {
            try {
                console.log('Setting up Echo channel: chat.{{ $booking->id }}');
                window.Echo.private('chat.{{ $booking->id }}').listen('.NewMessage', (e) => {
                        console.log('Message received via Echo:', e);
                        
                        // Create message element
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'mb-4 text-left';
                        
                        // Adjust classes for light/dark mode
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        const bgClass = isDarkMode ? 'bg-gray-700 text-white' : 'bg-gray-200';
                        const textClass = isDarkMode ? 'text-gray-400' : 'text-gray-500';
                        
                        messageDiv.innerHTML = `
                            <div class="inline-block rounded-lg px-4 py-2 max-w-xs lg:max-w-md ${bgClass}">
                                <p class="font-semibold text-sm">${e.sender_name}</p>
                                <p>${e.message}</p>
                                <p class="text-xs mt-1 ${textClass}">${new Date(e.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            </div>
                        `;
                        
                        // Add message to chat
                        chatMessages.appendChild(messageDiv);
                        
                        // Scroll to bottom
                        scrollToBottom();
                    });
                console.log('Echo channel setup complete');
            } catch (error) {
                console.error('Error setting up Echo:', error);
            }
        } else {
            console.error('Echo is not defined. Make sure Laravel Echo is properly configured.');
        }
        
        // Function to send message
        function sendMessage() {
            if (!messageInput.value.trim()) {
                console.log('Message input is empty, not sending');
                return;
            }
            
            const messageText = messageInput.value.trim();
            console.log('Sending message:', messageText);
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found!');
                alert('CSRF token not found. Please refresh the page.');
                return;
            }
            console.log('CSRF token found:', token.getAttribute('content'));
            
            // Send message via AJAX
            console.log('Sending fetch request to:', '{{ route("messages.store", $booking->id) }}');
            fetch('{{ route("messages.store", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: messageText
                })
            })
            .then(response => {
                console.log('Response received:', response);
                if (!response.ok) {
                    console.error('Response not OK:', response.status, response.statusText);
                    return response.text().then(text => {
                        throw new Error(`Server responded with ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent successfully, response data:', data);
                
                // Create message element
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-4 text-right';
                
                messageDiv.innerHTML = `
                    <div class="inline-block rounded-lg px-4 py-2 max-w-xs lg:max-w-md bg-blue-500 text-white">
                        <p class="font-semibold text-sm">You</p>
                        <p>${data.message}</p>
                        <p class="text-xs mt-1 text-blue-100">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                    </div>
                `;
                
                // Add message to chat
                chatMessages.appendChild(messageDiv);
                
                // Clear input
                messageInput.value = '';
                
                // Scroll to bottom
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message: ' + error.message);
            });
        }
        
        // Handle button click
        console.log('Adding click event listener to send button');
        sendButton.addEventListener('click', function(e) {
            console.log('Send button clicked');
            sendMessage();
        });
        
        // Handle enter key in input
        console.log('Adding keypress event listener to message input');
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Enter key pressed in message input');
                e.preventDefault();
                sendMessage();
            }
        });
    });
</script>
@endpush


@endsection