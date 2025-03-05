<!DOCTYPE html>
<html>
<head>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
 
    @yield('content')
    
    
    <script>
        console.log('Initializing Echo in layout');
        if (typeof Pusher === 'undefined') {
            console.error('Pusher is not loaded!');
        } else {
            console.log('Pusher loaded successfully');
            if (typeof Echo === 'undefined') {
                window.Echo = {
                    channels: {},
                    
                    private: function(channel) {
                        console.log('Creating simplified private channel:', channel);
                        
                        if (!this.channels[channel]) {
                            const pusher = new Pusher('4cb653de810be070150e', {
                                cluster: 'eu',
                                forceTLS: true,
                                authEndpoint: '/broadcasting/auth'
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