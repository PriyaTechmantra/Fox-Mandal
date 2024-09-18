<?php

namespace App\Http\Controllers\Api\Fms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CabBooking;
use App\Models\FlightBooking;
use App\Models\TrainBooking;
use App\Models\HotelBooking;
use App\Models\User;

class BookingHistoryController extends Controller
{
    public function getBookingHistory(Request $request)
    {
        $userId = $request->user_id;

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $cabBookings = CabBooking::where('user_id', $userId)->get();
        $flightBookings = FlightBooking::where('user_id', $userId)->get();
        $trainBookings = TrainBooking::where('user_id', $userId)->get();
        $hotelBookings = HotelBooking::where('user_id', $userId)->get();

        $response = [
            'cab_bookings' => $cabBookings->isNotEmpty() ? $cabBookings : 'No cab bookings found',
            'flight_bookings' => $flightBookings->isNotEmpty() ? $flightBookings : 'No flight bookings found',
            'train_bookings' => $trainBookings->isNotEmpty() ? $trainBookings : 'No train bookings found',
            'hotel_bookings' => $hotelBookings->isNotEmpty() ? $hotelBookings : 'No hotel bookings found',
            'user_details' => $user
        ];

        return response()->json([
            'status' => true,
            'message' => 'Booking history retrieved successfully.',
            'data' => $response
        ], 200);
    }

}