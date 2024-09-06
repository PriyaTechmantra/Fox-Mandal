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
                'message' => 'No bookmarks found for this user',
                'status' => false
            ], 404);
        }
        return response()->json(['message' => 'Bookmark list', 'data' => $wishlists  ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'book_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $wishlist = Bookmark::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);
        return response()->json(['message' => 'Book added to wishlist', 'wishlist' => $wishlist, 'status'=>true], 200);
    }
}
