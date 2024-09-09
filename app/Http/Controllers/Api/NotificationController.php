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
            return response()->json(['error' => $validator->errors(),'status' => false], 400);
        }
       
        try {
            $notification = LmsNotification::create([
                'book_id' => $request->book_id,
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'notification_type' => $request->notification_type,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Notification created successfully',
                'data' => $notification
            ],201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function notificationListByUser(Request $request)
    {
       
        $userId = $request->input('receiver_id');

        $data = LmsNotification::where('receiver_id', $userId)->with('book')
            ->get();

        if (!$data) {
            return response()->json(['status'=>false,'message' => 'Notification not found'], 404);
        }
        return response()->json([
            'status'=>true,
            'message' => 'List of book of user',
            'data' =>$data
        ], 200);
    }

    public function markAsRead(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:lms_notifications,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors(),'status' => false], 400);
    }

    $id = $request->id;

    $notification = LmsNotification::find($id);
    if (!$notification) {
        return response()->json(['status'=>false,'message' => 'Notification not found'], 404);
    }

    $notification->is_read = true;
    $notification->save();

    return response()->json(['status'=>true,'message' => 'Notification marked as read','data'=>$notification], 200);
}
}



