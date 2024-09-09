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
            return response()->json([
                'error' => $validator->errors(),
                'status' => false
            ], 400);
        }
        try {
            $phoneNumber = $request->input('mobile');

            $user = User::where('mobile', $phoneNumber)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found',
                    'status' => false
                ], 404);
            }

            $otp = random_int(1000, 9999);

            $updateSuccessful = $user->update(['otp' => $otp]);

            if (!$updateSuccessful) {
                return response()->json([
                    'message' => 'Failed to send OTP, please try again later',
                    'status' => false
                ], 500); 
            }

            return response()->json([
                'message' => 'OTP sent successfully',
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error('Book transfer error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during the book transfer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(),'status' => false], 400);
        }
        try{
            $phoneNumber = $request->mobile;
            $otp = $request->otp;

            $user = User::where('mobile', $phoneNumber)
                        ->where('otp', $otp)
                        ->first();
            if ($user) {
                return response()->json(['message' => 'OTP verified successfully',
                'status'=>true], 200);
            }else {
                return response()->json([
                    'message' => 'Invalid OTP or mobile number',
                    'status' => false
                ], 401);
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