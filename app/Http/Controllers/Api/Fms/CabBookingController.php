<?php

namespace App\Http\Controllers\Api\Fms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CabBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CabBookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'booking_type' => 'required|integer|in:1,2,3',
            'from_location' => 'required|string|max:255',
            'bill_to' => 'required|integer|in:1,2,3',
            'cab_type' => 'required|integer|in:1,2,3',
            'to_location' => [
                'required_if:booking_type,1,3',
                'nullable',
                'string',
                'max:255'
            ],
            'departure_date' => [
                'required_if:booking_type,1,3',
                'nullable',
                'date'
            ],
            'pickup_date' => [
                'required_if:booking_type,2',
                'nullable',
                'date',
            ],
            'pickup_time' => 'required|date_format:h:i A',
            'hours' => [
                'required_if:booking_type,2',
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $validatedData = $validator->validated();

        $departureDate = isset($validatedData['departure_date'])
            ? Carbon::parse($validatedData['departure_date'])->format('Y-m-d l')
            : null;

        $pickupDate = isset($validatedData['pickup_date'])
            ? Carbon::parse($validatedData['pickup_date'])->format('Y-m-d l')
            : null;

        $pickupTime = isset($validatedData['pickup_time'])
            ? Carbon::createFromFormat('h:i A', $validatedData['pickup_time'])->format('H:i:s')
            : null;

        $booking = CabBooking::create([
            'user_id' => $validatedData['user_id'],
            'bill_to' => $validatedData['bill_to'],
            'cab_type' => $validatedData['cab_type'],
            'booking_type' => $validatedData['booking_type'],
            'from_location' => $validatedData['from_location'],
            'to_location' => $validatedData['to_location']?? null,
            'departure_date' => $departureDate,
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'hours' => $validatedData['hours']?? null,
        ]);

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to book cab, please try again later'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cab booked successfully',
            'data' => $booking
        ]);
    }

    public function cancelCabBooking(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:cab_bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $cabBooking = CabBooking::find($id);

        if ($cabBooking->status == 2) {
            return response()->json([
                'status' => false,
                'message' => 'This booking is already cancelled.'
            ], 400);
        }

        $cabBooking->status = 2;
        $cabBooking->save();

        return response()->json([
            'status' => true,
            'message' => 'Cab booking cancelled successfully.',
            'data' => $cabBooking
        ], 200);
    }


    

}
