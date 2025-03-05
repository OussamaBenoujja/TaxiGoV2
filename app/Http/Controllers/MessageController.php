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
        
        
        if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
            abort(403, 'Unauthorized');
        }
        
       
        $otherUser = (Auth::id() == $booking->client_id) 
            ? User::find($booking->driver_id) 
            : User::find($booking->client_id);
            
       
        $messages = Message::where('booking_id', $bookingId)
            ->with('sender')
            ->orderBy('created_at')
            ->get();
            
        
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
        try {
            error_log('MessageController@store method called');
            error_log('Request data: ' . json_encode($request->all()));
            error_log('Booking ID: ' . $bookingId);
            
            $booking = Booking::findOrFail($bookingId);
            error_log('Booking found: ' . $booking->id);
            
            if (Auth::id() != $booking->client_id && Auth::id() != $booking->driver_id) {
                error_log('Unauthorized access attempt: User ' . Auth::id());
                abort(403, 'Unauthorized');
            }
            
            $request->validate([
                'message' => 'required|string',
            ]);
            error_log('Validation passed');
            
            $receiverId = (Auth::id() == $booking->client_id)
                ? $booking->driver_id
                : $booking->client_id;
            error_log('Receiver ID determined: ' . $receiverId);
            
            $message = Message::create([
                'booking_id' => $bookingId,
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'message' => $request->message,
            ]);
            error_log('Message created: ' . $message->id);
            
            $message->load('sender');
            error_log('Sender relationship loaded');
            
            try {
                error_log('About to broadcast message: ' . $message->id);
                error_log('Broadcast channel: chat.' . $message->booking_id);
                error_log('Event payload: ' . json_encode([
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                ]));
                
                if(broadcast(new NewMessage($message))->toOthers()){
                    error_log('Message broadcasted successfully');
                }
                
                error_log('Message broadcasted successfully');
                
                // Add verification that broadcast service is configured
                $broadcastDriver = config('broadcasting.default');
                error_log('Broadcast driver: ' . $broadcastDriver);
                
                if ($broadcastDriver === 'pusher') {
                    $pusherConfig = config('broadcasting.connections.pusher');
                    error_log('Pusher app ID configured: ' . ($pusherConfig['app_id'] ? 'Yes' : 'No'));
                    error_log('Pusher configured with encryption: ' . ($pusherConfig['encrypted'] ? 'Yes' : 'No'));
                }
            } catch (\Exception $e) {
                error_log('Broadcast error: ' . $e->getMessage());
                error_log('Broadcast error trace: ' . $e->getTraceAsString());
            }
            
            error_log('Returning JSON response');
            return response()->json($message);
        } catch (\Exception $e) {
            error_log('Exception in MessageController@store: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function unreadCount(): JsonResponse
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['unreadCount' => $unreadCount]);
    }
}
