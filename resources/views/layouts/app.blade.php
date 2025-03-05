<!DOCTYPE html>
<html>
<head>
    <!-- Other head elements -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Include Pusher directly from CDN -->
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Your content -->
    @yield('content')
    
    <!-- Initialize Echo directly in the layout -->
    <script>
        console.log('Initializing Echo in layout');
        
        // Check if Pusher loaded correctly
        if (typeof Pusher === 'undefined') {
            console.error('Pusher is not loaded!');
        } else {
            console.log('Pusher loaded successfully');
            
            // If laravel-echo is not available, create a simplified version
            if (typeof Echo === 'undefined') {
                window.Echo = {
                    channels: {},
                    
                    private: function(channel) {
                        console.log('Creating simplified private channel:', channel);
                        
                        if (!this.channels[channel]) {
                            const pusher = new Pusher('4cb653de810be070150e', {
                                cluster: 'eu',
                                forceTLS: true
                            });
                            
                            this.channels[channel] = pusher.subscribe('private-' + channel);
                        }
                        
                        return {
                            listen: (event, callback) => {
                                console.log('Listening for event on channel:', event, channel);
                                this.channels[channel].bind(event, callback);
                                return this;
                            }
                        };
                    }
                };
                
                console.log('Created simplified Echo implementation');
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>