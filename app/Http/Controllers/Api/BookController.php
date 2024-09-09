<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Bookshelve;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::get();
        if ($books) {
             return response()->json(['status'=>true,'message' => 'List of book','data' => $books ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book list found'
            ], 404);
        }
    }

    public function bookWithIssuedBook(Request $request)
    {
        $books = Book::with('issuebook')->get();
        if ($books) {
            return response()->json(['status'=>true,'message' => 'List of book with issue details','data' => $books, ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book list found'
            ], 404);
        }
    }
    public function bookDetails(Request $request)
    {
  
        try {
            $book = Book::findOrFail($request->id);
            if ($book) {
            return response()->json([
                'status'=>true,
                'message' => 'Detail of book',
                'data' => $book
            ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Details not found'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the book details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   

    public function search(Request $request)
    {
        try{
            $title = $request->input('title');
            $publisher = $request->input('publisher');
            $author = $request->input('author');
            $uid = $request->input('uid');

            $books = Book::query();

            if ($title) {
                $books->where('title', 'LIKE', "%{$title}%");
            }
            if ($publisher) {
                $books->where('publisher', 'LIKE', "%{$publisher}%");
            }
            if ($author) {
                $books->where('author', 'LIKE', "%{$author}%");
            }
            if ($uid) {
                $books->where('uid', 'LIKE', "%{$uid}%");
            }

            $books = $books->with('issuebook')->get();
            if ($books) {
                return response()->json([
                    'status'=>true,
                    'message' => 'List of search data', 
                    'data' => $books
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data found'
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


    public function searchDetailsByQrCode(Request $request)
    {
        try{
            $qrcode = $request->input('qrcode');

            $book = Book::where('qrcode', $qrcode)->with('category')->with('bookshelves')
                ->first();
            if ($book) {
                return response()->json([
                    'status'=>true,
                    'message' => 'Details of book by Qr-Code',
                    'data' =>$book
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Details not found'
                ], 404);
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
    public function CategoryWiseBookList(Request $request)
    {
       
        $book = Book::where('category_id', $request->category_id)
            ->get();

        if ($book->isNotEmpty()) {
            return response()->json([
                'status'=>true,
                'message' => 'Book list by category wise',
                'data' =>$book
            ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
            ], 404);
        }
    }

    public function showBooksByBookShelveQRCode(Request $request)
    {
        $bookshelve = Bookshelve::where('qrcode', $request->qrcode)->first();

        if (!$bookshelve) {
            return response()->json(['message' => 'Bookshelf not found','status'=>false], 404);
        }

        $books = $bookshelve->books()->with(['office', 'category'])->get();
        if ($books) {
            return response()->json([
                'books' => $books->map(function ($book) {
                    return [
                        'status'=>true,
                        'message' => 'Book list by shelve QR-code wise',
                        'data'=> $book
                    ];
                })
            ],200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
                
            ], 404);
        }
    }

   
    public function showBooksByBookShelve(Request $request)
    {
        $bookshelve = Bookshelve::where('number', $request->number)->first();

        if (!$bookshelve) {
            return response()->json(['message' => 'Bookshelf not found','status'=>false], 404);
        }

        $books = $bookshelve->books()->get();
        if ($books) {
        return response()->json([
            'status'=>true,
            'message' => 'Book list by shelve QR-code wise', 
            
            'books' => $books->map(function ($book) {
                return [
                    'data'=> $book,
                ];
            })
        ],200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Book not found'
               
            ], 404);
        }

    }


}
 