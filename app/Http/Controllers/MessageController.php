<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index($bookingId): View
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Make sure the user is either the client or driver of this booking
        if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
            abort(403, 'Unauthorized');
        }
        
        // Get the other user (if client, get driver and vice versa)
        $otherUser = (Auth::id() == $booking->client_id) 
            ? User::find($booking->driver_id) 
            : User::find($booking->client_id);
            
        // Get all messages for this booking
        $messages = Message::where('booking_id', $bookingId)
            ->with('sender')
            ->orderBy('created_at')
            ->get();
            
        // Mark all messages as read where the current user is the receiver
        Message::where('booking_id', $bookingId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('messages.index', [
            'booking' => $booking,
            'otherUser' => $otherUser,
            'messages' => $messages
        ]);
    }
    
    public function store(Request $request, $bookingId): JsonResponse
    {
        $booking = Booking::findOrFail($bookingId);
        
        // Make sure the user is either the client or driver of this booking
        if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'message' => 'required|string',
        ]);
        
        // Determine receiver (if sender is client, receiver is driver and vice versa)
        $receiverId = (Auth::id() == $booking->client_id) 
            ? $booking->driver_id 
            : $booking->client_id;
        
        $message = Message::create([
            'booking_id' => $bookingId,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);
        
        // Load sender relationship for the event
        $message->load('sender');
        
        // Broadcast the message
        broadcast(new NewMessage($message))->toOthers();
        
        return response()->json($message);
    }
    
    public function unreadCount(): JsonResponse
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['unreadCount' => $unreadCount]);
    }
}
