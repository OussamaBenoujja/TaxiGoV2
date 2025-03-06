<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

class QRCodeController extends Controller
{
    /**
     * Generate a QR code for user profile
     *
     * @param int|null $userId
     * @return \Illuminate\View\View
     */
    public function showProfileQR($userId = null)
    {
        // If no userId provided, use authenticated user
        if (!$userId) {
            $user = Auth::user();
        } else {
            $user = User::findOrFail($userId);
        }

       
        // Generate QR code content (e.g., profile URL)
        $qrCodeContent = $user->profile_url ?? route('/profile/qr-code', $user->id);

        // Create QR code
        $qrCode = QrCode::create($qrCodeContent)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Write QR code
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Convert to base64 for easy embedding
        $qrCodeImage = base64_encode($result->getString());

        return view('profile.qr-code', [
            'qrCodeImage' => $qrCodeImage,
            'user' => $user
        ]);
    }
}