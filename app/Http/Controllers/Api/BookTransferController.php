<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookTransfer;
use App\Models\User;
use App\Models\LmcNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class BookTransferController extends Controller
{
    public function transferBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required', 
            'from_user_id' => 'required',
            'to_user_id' => 'required', 
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        DB::beginTransaction(); 

        try {
            $bookTransfer = BookTransfer::create([
                'book_id' => $request->book_id,
                'is_transfer' => 1,
                'from_user_id' => $request->from_user_id,
                'to_user_id' => $request->to_user_id,
                'transfer_date' => now()->toDateString(),
            ]);

            $notificationDataFrom = [
                'title' => 'Book Transfer Notification',
                'body' => 'A book has been transferred from your account.',
                'data' => [
                    'book_id' => $request->book_id,
                    'from_user_id' => $request->from_user_id,
                    'to_user_id' => $request->to_user_id,
                ],
            ];

           
            DB::commit(); 

            return response()->json([
                'status'=>true,
                'message' => 'Book transfer status updated successfully.',
                'data' => $bookTransfer,
                
            ], 201); 
            if (!$bookTransfer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to book transfer status updated ',
                    
                ], 500); 
            }

        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error('Book transfer error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during the book transfer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    
}
 