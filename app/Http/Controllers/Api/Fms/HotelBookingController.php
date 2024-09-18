<?php

namespace App\Http\Controllers\Api\Fms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelBooking;
use App\Models\Room;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HotelBookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'property_id' => 'required|exists:properties,id',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'guest_number' => 'required|integer|min:1',
            'room_number' => 'required|integer|min:1'
            
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }
    
        $validatedData = $validator->validated();
    
        $checkinDate = Carbon::parse($validatedData['checkin_date'])->format('Y-m-d l');
        $checkoutDate = Carbon::parse($validatedData['checkout_date'])->format('Y-m-d l');
    
        $booking = HotelBooking::create([
            'user_id' => $validatedData['user_id'], 
            'room_id' => $validatedData['room_id'],
            'property_id' => $validatedData['property_id'],
            'checkin_date' => $checkinDate,  
            'checkout_date' => $checkoutDate, 
            'guest_number' => $validatedData['guest_number'],
            'room_number' => $validatedData['room_number'],

        ]);
    
        return response()->json(['status' => true, 'message' => 'Booking created successfully', 'data' => $booking], 201);
    }

    public function roomList(Request $request)
    {
       $data=Room::get();
    

        if ($data) {
            return response()->json(['status'=>true,'message' => 'List of room','data' => $data ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Room list not found'
            ], 404);
        }
    }

    public function propertyList(Request $request)
    {
       $data=Property::get();
    

        if ($data) {
            return response()->json(['status'=>true,'message' => 'List of property','data' => $data ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Property list not found'
            ], 404);
        }
    }
    public function userRoomBookings(Request $request)
    {
        $userId=$request->user_id;
        $bookings = HotelBooking::with(['room', 'property','user'])
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No bookings found for this user.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $bookings,
        ], 200);
    }

    public function cancelHotelBooking(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:hotel_bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        $hotelBooking = HotelBooking::find($id);

        if ($hotelBooking->status == 2) {
            return response()->json([
                'status' => false,
                'message' => 'This hotel booking is already cancelled.'
            ], 400);
        }

        $hotelBooking->status = 2;
        $hotelBooking->save();

        return response()->json([
            'status' => true,
            'message' => 'Hotel booking cancelled successfully.',
            'data' => $hotelBooking
        ], 200);
    }


    
}
