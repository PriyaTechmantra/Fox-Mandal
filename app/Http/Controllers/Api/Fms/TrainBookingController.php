<?php

namespace App\Http\Controllers\Api\Fms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainBooking;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TrainBookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'travel_date' => 'required|date',
            'bill_to' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $validatedData = $validator->validated();

        $travelDate = Carbon::parse($validatedData['travel_date'])->format('Y-m-d l');

        $trainBooking = TrainBooking::create([
            'user_id' => $validatedData['user_id'],
            'from' => $validatedData['from'],
            'to' => $validatedData['to'],
            'travel_date' => $travelDate,
            'bill_to' => $validatedData['bill_to'],
        ]);

        if (!$trainBooking) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create train booking. Please try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Train booking created successfully.',
            'data' => $trainBooking,
        ], 201);
    }

    public function cancelTrainBooking(Request $request)
    {
        $id = $request->id;
    
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:train_bookings,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }
    
        $trainBooking = TrainBooking::find($id);
    
        if ($trainBooking->status == 2) {
            return response()->json([
                'status' => false,
                'message' => 'This booking is already cancelled.'
            ], 400);
        }
    
        $trainBooking->status = 2;
        $trainBooking->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Booking cancelled successfully.',
            'data' => $trainBooking
        ], 200);

        if (!$trainBooking) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancelled booking, please try again later'
            ], 500);
        }
    }
    


}
