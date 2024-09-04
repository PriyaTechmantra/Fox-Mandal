<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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


    // public function sendPushNotification($fcmToken, $title, $body)
    // {
    //     $serverKey = 'YOUR_FIREBASE_SERVER_KEY'; // Replace with your FCM server key

    //     $data = [
    //         "registration_ids" => [$fcmToken], // or use "to" => $fcmToken for a single device
    //         "notification" => [
    //             "title" => $title,
    //             "body" => $body,
    //             "sound" => "default", // optional
    //         ],
    //     ];

    //     $headers = [
    //         'Authorization: key=' . $serverKey,
    //         'Content-Type: application/json',
    //     ];

    //     $response = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $data);

    //     if ($response->successful()) {
    //         return response()->json(['success' => 'Notification sent successfully']);
    //     }

    //     return response()->json(['error' => 'Failed to send notification'], 500);
    // }

    public function sendPushNotification($fcmToken, $title, $body)
    {
        $serverKey = 'YOUR_FIREBASE_SERVER_KEY'; // Replace with your actual Firebase server key
    
        $data = [
            "to" => $fcmToken, // Send to a single device
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default", // Optional: Play sound on notification
            ],
        ];
    
        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        // Send the POST request to Firebase FCM
        $response = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $data);
    
        if ($response->failed()) {
            // Handle notification failure if necessary
            Log::error('Failed to send notification', ['response' => $response->body()]);
        }
    }
    

}



