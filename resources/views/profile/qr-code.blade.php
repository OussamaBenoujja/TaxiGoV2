@extends('layouts.theme')

@section('content')
<div class="bg-gray-950 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Your Profile QR Code</h1>
                <p class="mt-2 text-gray-400">Share your profile easily with others</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <a href="{{ route('profile.edit') }}" class="btn-secondary flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Profile
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-white">{{ $user->name }}'s Profile QR Code</h2>
            </div>
            
            <div class="p-6 flex flex-col items-center">
                <!-- QR Code Image -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="Profile QR Code" id="qr-code-image" class="w-64 h-64">
                </div>
                
                <div class="text-center mb-6">
                    <p class="text-gray-300 mb-2">Scan this QR code to visit my profile</p>
                    <div class="bg-gray-800 rounded-lg p-3 text-gray-300 break-all">
                        <span id="profile-url">{{ $profileUrl }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button id="download-btn" class="btn-primary flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Download QR Code
                    </button>
                    
                    <button id="copy-link-btn" class="btn-secondary flex items-center justify-center">
                        <i class="fas fa-link mr-2"></i>
                        Copy Profile Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Download QR Code functionality
        document.getElementById('download-btn').addEventListener('click', function() {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const image = document.getElementById('qr-code-image');
            
            canvas.width = image.width;
            canvas.height = image.height;
            
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            
            // Create download link
            const downloadLink = document.createElement('a');
            downloadLink.download = '{{ $user->name }}_profile_qr.png';
            downloadLink.href = canvas.toDataURL('image/png');
            downloadLink.click();
        });
        
        // Copy profile link functionality
        document.getElementById('copy-link-btn').addEventListener('click', function() {
            const profileUrl = document.getElementById('profile-url').textContent;
            
            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.value = profileUrl;
            document.body.appendChild(tempInput);
            
            // Select and copy the text
            tempInput.select();
            document.execCommand('copy');
            
            // Remove the temporary element
            document.body.removeChild(tempInput);
            
            // Show feedback
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check mr-2"></i> Link Copied!';
            
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    });
</script>
@endpush
@endsection