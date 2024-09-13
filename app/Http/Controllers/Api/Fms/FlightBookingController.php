<?php

namespace App\Http\Controllers\Api\Fms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlightBooking; 
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FlightBookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', 
            'trip_type' => 'required|integer|in:1,2', 
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255', 
            'departure_date' => 'required|date',
            'return_date' => 'required_if:trip_type,2|nullable|date',
            'traveler_number' => 'required|integer|min:1',
            'bill_to' => 'required|integer|in:1,2,3', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $validatedData = $validator->validated();

        $departureDate = isset($validatedData['departure_date'])
            ? Carbon::parse($validatedData['departure_date'])->format('Y-m-d l')
            : null;
    
        $returnDate = isset($validatedData['return_date'])
            ? Carbon::parse($validatedData['return_date'])->format('Y-m-d l')
            : null;
    
    
        $flightBooking = FlightBooking::create([
            'user_id' => $validatedData['user_id'],
            'trip_type' => $validatedData['trip_type'],
            'from' => $validatedData['from'],
            'to' => $validatedData['to'],
            'departure_date' => $departureDate,
            'return_date' => $returnDate?? null, 
            'traveler_number' => $validatedData['traveler_number'],
            'bill_to' => $validatedData['bill_to'],
        ]);

        if (!$flightBooking) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create flight booking. Please try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Flight booking created successfully.',
            'data' => $flightBooking,
        ], 201);
    }
}
