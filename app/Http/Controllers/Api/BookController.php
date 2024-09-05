<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Bookshelve;

class BookController extends Controller
{
    

    public function index(Request $request)
    {
  
        $query = $request->input('query');

        $books = Book::get();

        return response()->json(['message' => 'List of book','data' => $books], 200);
    }

    public function bookWithIssuedBook(Request $request)
    {
  
        $query = $request->input('query');

        $books = Book::with('issuebook')->get();

        return response()->json(['message' => 'List of book','data' => $books], 200);
    }
    public function bookDetails(Request $request)
    {
  
        $books = Book::findOrFail($request->id);

        return response()->json(['message' => 'Detail of book',$books], 200);
    }
   

    public function search(Request $request)
{
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

    return response()->json(['message' => 'List of search data', 'data' => $books], 200);
}


    public function searchDetailsByQrCode(Request $request)
    {
  
        $qrcode = $request->input('qrcode');

        $book = Book::where('qrcode', $qrcode)->with('category')->with('bookshelves')
            ->first();

        return response()->json([
                                    'message' => 'Details of book by Qr-Code',
                                    'data' =>$book
                                ], 200);
    }
    public function CategoryWiseBookList(Request $request)
    {
  

        $book = Book::where('category_id', $request->category_id)
            ->get();

        return response()->json([
                                    'message' => 'Book list by category wise',
                                    'data' =>$book
                                ], 200);
    }

    public function showBooksByBookShelveQRCode(Request $request)
    {
        // Find the bookshelf by its QR code
        $bookshelve = Bookshelve::where('qrcode', $request->qrcode)->first();

        // Check if the bookshelf exists
        if (!$bookshelve) {
            return response()->json(['message' => 'Bookshelf not found'], 404);
        }

        // Retrieve the books related to the found bookshelf, with their office and category details
        $books = $bookshelve->books()->with(['office', 'category'])->get();

        // Format and return the book details as a JSON response
        return response()->json([
            'books' => $books->map(function ($book) {
                return [
                    'message' => 'Book list by shelve QR-code wise',
                    'data'=> $book
                    // 'id' => $book->id,
                    // 'title' => $book->title,
                    // 'author' => $book->author,
                    // 'publisher' => $book->publisher,
                    // 'edition' => $book->edition,
                    // 'quantity' => $book->quantity,
                    // 'office' => [
                    //     'id' => $book->office->id,
                    //     'name' => $book->office->name,
                    //     'location' => $book->office->location,
                    // ],
                    // 'category' => [
                    //     'id' => $book->category->id,
                    //     'name' => $book->category->name,
                    //     'description' => $book->category->description,
                    // ],
                ];
            })
        ],200);
    }

    public function showBooksByBookShelve(Request $request)
    {
        $bookshelve = Bookshelve::where('number', $request->number)->first();

        if (!$bookshelve) {
            return response()->json(['message' => 'Bookshelf not found'], 404);
        }

        $books = $bookshelve->books()->get();

        return response()->json([
            'books' => $books->map(function ($book) {
                return [
                    'message' => 'Book list by shelve QR-code wise',
                    'data'=> $book
                ];
            })
        ],200);

    }


}
 