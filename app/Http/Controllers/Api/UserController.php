<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $data = User::where('id',$request->id)->first();
        if ($data) {
             return response()->json(['status'=>true,'message' => 'List of book','data' => $data ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book list not found'
            ], 404);
        }
    }

    public function searchMember(Request $request)
    {
        try {
            $keyword = $request->input('keyword');

            $data = User::where('status', 1);

            if ($keyword) {
                $data->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('mobile', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%");
                });
            }

            $data = $data->get();

            if ($data->isNotEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Search results found: ' . $data->count() . ' record(s)',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data found'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Member search error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred during the search.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    

}
 