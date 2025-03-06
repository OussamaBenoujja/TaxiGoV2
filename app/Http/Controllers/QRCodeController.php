<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
            $userId = Auth::id();
        }

        $user = User::findOrFail($userId);

        // Only allow viewing QR if it's the user's own profile or if the profile is public
        if (Auth::id() !== $user->id) {
            return redirect()->route('profiles.public', $user->id);
        }

        // Generate profile URL
        $profileUrl = route('profiles.public', $user->id);

        // Generate QR code
        $qrCode = QrCode::create($profileUrl)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Convert to base64
        $qrCode = base64_encode($result->getString());

        return view('profile.qr-code', compact('user', 'profileUrl', 'qrCode'));
    }
}