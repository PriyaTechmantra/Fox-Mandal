<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class BookTransferController extends Controller
{
    public function transferBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required', 
            'from_user_id' => 'required',
            'to_user_id' => 'required', 
        ]);

        DB::beginTransaction(); 

        try {
            $bookTransfer = BookTransfer::create([
                'book_id' => $validated['book_id'],
                'is_transfer' => 1,
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'transfer_date' => now()->toDateString(),
            ]);

            $notificationDataFrom = [
                'title' => 'Book Transfer Notification',
                'body' => 'A book has been transferred from your account.',
                'data' => [
                    'book_id' => $validated['book_id'],
                    'from_user_id' => $validated['from_user_id'],
                    'to_user_id' => $validated['to_user_id'],
                ],
            ];

            $notificationDataTo = [
                'title' => 'Book Transfer Notification',
                'body' => 'A book has been transferred to your account.',
                'data' => [
                    'book_id' => $validated['book_id'],
                    'from_user_id' => $validated['from_user_id'],
                    'to_user_id' => $validated['to_user_id'],
                ],
            ];

            $fromUser = User::find($validated['from_user_id']);
            if ($fromUser && $fromUser->fcm_token) {
                $this->sendPushNotification(
                    $fromUser->fcm_token,
                    $notificationDataFrom['title'],
                    $notificationDataFrom['body']
                );
            }

            $toUser = User::find($validated['to_user_id']);
            if ($toUser && $toUser->fcm_token) {
                $this->sendPushNotification(
                    $toUser->fcm_token,
                    $notificationDataTo['title'],
                    $notificationDataTo['body']
                );
            }

            DB::commit(); 

            return response()->json([
                'message' => 'Book transfer status updated successfully.',
                'data' => $bookTransfer,
                'notification' => $notificationDataTo, 
            ], 201); 

        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error('Book transfer error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during the book transfer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendPushNotification($fcmToken, $title, $body)
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
            Log::error('Failed to send notification: ' . $response->body());
        }
    }
}
