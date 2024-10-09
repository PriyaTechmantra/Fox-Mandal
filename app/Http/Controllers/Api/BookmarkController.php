<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $wishlists = Bookmark::where('user_id', $request->user_id)->with('book')->get();
        if ($wishlists->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No bookmarks found for this user'
                
            ], 404);
        }
        return response()->json(['status'=>true,'message' => 'Bookmark list', 'data' => $wishlists ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'book_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false,'error' => $validator->errors()], 400);
        }

        $wishlist = Bookmark::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);
        return response()->json([ 'status'=>true,'message' => 'Book added to wishlist', 'wishlist' => $wishlist], 201);

        if (!$wishlist) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to book added to wishlist',
                
            ], 500); 
        }
    }
    public function destroy(Request $request)
    {
        $id = $request->id;  
        $userId = $request->user_id;  

        $bookmark = Bookmark::find($id);

        if (!$bookmark) {
            return response()->json([ 'status' => false,'message' => 'Bookmark not found'], 404);
        }

        if ($bookmark->user_id !== $userId) {
            return response()->json([ 'status' => false,'message' => 'Unauthorized'], 403);
        }

        $bookmark->delete();

        return response()->json(['status'=>true,'message' => 'Bookmark removed successfully'], 200);
    }
}
