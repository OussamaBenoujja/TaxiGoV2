@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card flex flex-col h-[calc(100vh-12rem)]">
            <!-- Chat Header -->
            <div class="card-header flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-white">Chat with {{ $otherUser->name }}</h1>
                    <p class="text-sm text-gray-400">
                        Booking #{{ $booking->id }} - 
                        {{ \Carbon\Carbon::parse($booking->pickup_time)->format('M d, Y h:i A') }}
                    </p>
                </div>
                <a href="{{ url()->previous() }}" class="btn-secondary flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
            
            <!-- Chat Messages -->
            <div id="chat-messages" class="flex-grow overflow-y-auto p-4 space-y-4 bg-gray-950">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="relative max-w-xl px-4 py-2 rounded-lg shadow 
                            {{ $message->sender_id == Auth::id() ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-white' }}">
                            <div class="font-medium text-sm">
                                {{ $message->sender_id == Auth::id() ? 'You' : $message->sender->name }}
                            </div>
                            <div class="mt-1">{{ $message->message }}</div>
                            <div class="mt-1 text-xs {{ $message->sender_id == Auth::id() ? 'text-yellow-900' : 'text-gray-400' }}">
                                {{ $message->created_at->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Chat Input -->
            <div class="border-t border-gray-800 p-4">
                <form id="message-form" class="flex" onsubmit="return false;">
                    @csrf
                    <div class="relative flex-grow">
                        <input 
                            type="text" 
                            id="message-input" 
                            name="message" 
                            class="form-input pr-10" 
                            placeholder="Type your message..."
                            required
                        >
                        <button 
                            type="button" 
                            id="send-message-btn"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-yellow-500 hover:text-yellow-400"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded for chat page');
    
    // First, let's check if Echo is defined globally
    if (typeof window.Echo === 'undefined') {
        console.error('❌ Echo is not available! Setting up fallback...');
        
        // Setup a fallback Echo instance - should only run if main Echo setup failed
        try {
            const pusherKey = '{{ env("PUSHER_APP_KEY") }}';
            const pusherCluster = '{{ env("PUSHER_APP_CLUSTER") }}';
            
            console.log('Setting up fallback Echo with:', { 
                key: pusherKey, 
                cluster: pusherCluster 
            });
            
            // Only setup fallback if we have a key
            if (pusherKey) {
                window.Echo = new window.Echo({
                    broadcaster: 'pusher',
                    key: pusherKey,
                    cluster: pusherCluster,
                    forceTLS: true,
                    authEndpoint: '/broadcasting/auth',
                    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                });
                
                console.log('✅ Fallback Echo setup complete');
            } else {
                console.error('❌ No Pusher key available for fallback Echo');
            }
        } catch (e) {
            console.error('❌ Failed to set up fallback Echo:', e);
        }
    } else {
        console.log('✅ Echo is available globally');
    }
    
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-message-btn');
    const chatMessages = document.getElementById('chat-messages');

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    scrollToBottom();
    
    // Set up Echo listeners
    console.log('Setting up channel listener for: chat.{{ $booking->id }}');
    
    if (window.Echo) {
        try {
            const channel = window.Echo.private(`chat.{{ $booking->id }}`);
            
            channel.subscribed(() => {
                console.log('✅ Successfully subscribed to channel: chat.{{ $booking->id }}');
            }).error((error) => {
                console.error('❌ Error subscribing to channel:', error);
            });
            
            channel.listen('NewMessage', (e) => {
                console.log('📨 NewMessage event received:', e);
                
                // Skip self-messages (already handled by the UI)
                if (e.sender_id == {{ Auth::id() }}) {
                    console.log('Skipping own message');
                    return;
                }
    
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex justify-start';
                
                let timestamp = '';
                try {
                    timestamp = new Date(e.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                } catch (err) {
                    console.error('Error formatting timestamp:', err);
                    timestamp = 'just now';
                }
                
                messageDiv.innerHTML = `
                    <div class="relative max-w-xl px-4 py-2 rounded-lg shadow bg-gray-800 text-white">
                        <div class="font-medium text-sm">${e.sender_name}</div>
                        <div class="mt-1">${e.message}</div>
                        <div class="mt-1 text-xs text-gray-400">
                            ${timestamp}
                        </div>
                    </div>
                `;
                
                chatMessages.appendChild(messageDiv);
                messageDiv.style.marginTop = '1rem';
                scrollToBottom();
            });
            
            console.log('✅ Echo listener setup complete');
        } catch (error) {
            console.error('❌ Error setting up Echo:', error);
        }
    } else {
        console.error('❌ Echo is still not available after fallback attempt. Real-time messaging will not work.');
    }
    
    function sendMessage() {
        if (!messageInput.value.trim()) return;
        
        const messageText = messageInput.value.trim();
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (!token) {
            console.error('CSRF token not found. Please refresh the page.');
            return;
        }
        
        console.log('Sending message:', messageText);
        
        fetch('{{ route("messages.store", $booking->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: messageText
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Server responded with ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Message sent successfully:', data);
            
            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            
            // Format timestamp safely
            let timestamp = '';
            try {
                timestamp = new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            } catch (err) {
                console.error('Error formatting timestamp:', err);
                timestamp = 'just now';
            }
            
            messageDiv.innerHTML = `
                <div class="relative max-w-xl px-4 py-2 rounded-lg shadow bg-yellow-500 text-black">
                    <div class="font-medium text-sm">You</div>
                    <div class="mt-1">${data.message}</div>
                    <div class="mt-1 text-xs text-yellow-900">
                        ${timestamp}
                    </div>
                </div>
            `;
            
            // Add message to chat
            chatMessages.appendChild(messageDiv);
            
            // Add some spacing between messages
            messageDiv.style.marginTop = '1rem';
            
            // Clear input
            messageInput.value = '';
            
            // Scroll to bottom
            scrollToBottom();
        })
        .catch(error => {
            console.error('❌ Error sending message:', error);
            // Replace alert with a more user-friendly approach
            const errorDiv = document.createElement('div');
            errorDiv.className = 'p-2 mb-2 text-sm text-red-500 bg-red-100 rounded';
            errorDiv.textContent = 'Error sending message. Please try again.';
            chatMessages.appendChild(errorDiv);
            
            // Remove error message after 3 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
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