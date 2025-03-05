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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-message-btn');
        const chatMessages = document.getElementById('chat-messages');
        
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Scroll to bottom on page load
        scrollToBottom();
        
        // Initialize Echo and listen for messages
        if (window.Echo) {
            try {
                window.Echo.private('chat.{{ $booking->id }}')
                    .listen('NewMessage', (e) => {
                        console.log('Message received:', e);
                        
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
            } catch (error) {
                console.error('Error setting up Echo:', error);
            }
        } else {
            console.error('Echo is not defined. Make sure Laravel Echo is properly configured.');
        }
        
        // Function to send message
        function sendMessage() {
            if (!messageInput.value.trim()) return;
            
            console.log('Sending message:', messageInput.value);
            
            // Send message via AJAX
            fetch('{{ route("messages.store", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: messageInput.value
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent successfully:', data);
                
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
                alert('Error sending message. Please try again.');
            });
        }
        
        // Handle button click
        sendButton.addEventListener('click', sendMessage);
        
        // Handle enter key in input
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    });
</script>
@endpush


@endsection