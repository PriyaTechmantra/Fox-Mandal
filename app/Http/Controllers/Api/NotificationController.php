<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LmsNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function saveToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $user = User::find($request->id);
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json(['success' => 'Token saved successfully', 'status'=>true]);
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
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
            'notification_type' => 'required|integer|in:1,2,3',
        ]);
       

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
       
        $notification = LmsNotification::create([
            'book_id' => $request->book_id,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'notification_type' => $request->notification_type,
        ]);

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification, 
            'status'=>true
        ], 201);
    }

    public function notificationListByUser(Request $request)
    {
       
        $userId = $request->input('receiver_id');

        $books = LmsNotification::where('receiver_id', $userId)->with('book')
            ->get();

       
        return response()->json([
            'message' => 'List of book of user',
            'data' =>$books, 
            'status'=>true
        ], 200);
    }

    public function markAsRead(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:lms_notifications,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $id = $request->id;

    $notification = LmsNotification::find($id);
    if (!$notification) {
        return response()->json(['message' => 'Notification not found', 'status'=>true], 200);
    }

    $notification->is_read = true;
    $notification->save();

    return response()->json(['message' => 'Notification marked as read', 'status'=>true], 200);
}
}



