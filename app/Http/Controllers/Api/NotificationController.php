<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LmsNotification;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'fcm_token' => 'required|string',
        ]);

        $user = User::find($request->id);
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json(['success' => 'Token saved successfully']);
        }

        return response()->json(['error' => 'User not found'], 404);
    }


   
    public function sendPushNotification($fcmToken, $title, $body)
    {
        $serverKey = 'YOUR_FIREBASE_SERVER_KEY'; 
    
        $data = [
            "to" => $fcmToken, 
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default", 
            ],
        ];
    
        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $response = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $data);
    
        if ($response->failed()) {
            Log::error('Failed to send notification', ['response' => $response->body()]);
        }
    }

    public function notification(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
            'notification_type' => 'required|integer|in:1,2,3',
        ]);

       
        $notification = LmsNotification::create([
            'book_id' => $validated['book_id'],
            'sender_id' => $validated['sender_id'],
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'notification_type' => $validated['notification_type'],
        ]);

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

    public function notificationListByUser(Request $request)
    {
       
        $userId = $request->input('receiver_id');

        $books = LmsNotification::where('receiver_id', $userId)->with('book')
            ->get();

       
        return response()->json([
            'message' => 'List of book of user',
            'data' =>$books
        ], 200);
    }

    public function markAsRead(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|integer|exists:lms_notifications,id',
    ]);

    $id = $validated['id'];

    $notification = LmsNotification::find($id);
    if (!$notification) {
        return response()->json(['message' => 'Notification not found'], 200);
    }

    $notification->is_read = true;
    $notification->save();

    return response()->json(['message' => 'Notification marked as read'], 200);
}
}



