<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeController extends Controller
{
    public function showProfileQR($userId = null)
    {
        if (!$userId) {
            $user = Auth::user();
        } else {
            $user = User::findOrFail($userId);
        }
        
        $profileUrl = route('profiles.public', $user->id);
        
        // Create QR code with the new implementation
        $qrCode = new QrCode(
            data: $profileUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        
        // Create writer and generate the QR code
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Get QR code as base64 encoded string
        $qrCodeImage = base64_encode($result->getString());
        
        return view('profile.qr-code', [
            'qrCode' => $qrCodeImage,
            'user' => $user,
            'profileUrl' => $profileUrl
        ]);
    }
}