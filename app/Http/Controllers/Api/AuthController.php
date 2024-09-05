<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $phoneNumber = $request->mobile;

        $user = User::where('mobile', $phoneNumber)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $otp = random_int(1000, 9999);
        $user->update([
            'otp' => $otp,
        ]);

        return response()->json(['message' => 'OTP sent successfully', 
        'otp' => $otp,
        'name'=> $user->name,
        'email'=> $user->email,
        'mobile'=> $user->mobile,
    ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $phoneNumber = $request->mobile;
        $otp = $request->otp;

        $user = User::where('mobile', $phoneNumber)
                    ->where('otp', $otp)
                    ->first();
        if ($user) {
            

            return response()->json(['message' => 'OTP verified successfully'], 200);
        }

    }

}