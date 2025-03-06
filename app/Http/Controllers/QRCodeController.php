<?php
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller;

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

        // Build QR code using the new Builder instance (version 6.0)
        $result = (new Builder())
            ->writer(new PngWriter())
            ->data($profileUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->build();

        // Convert to base64 for embedding if needed
        $qrCodeBase64 = base64_encode($result->getString());

        return view('profile.qr-code', [
            'user' => $user,
            'profileUrl' => $profileUrl,
            'qrCode' => $qrCodeBase64,
        ]);
    }
}
