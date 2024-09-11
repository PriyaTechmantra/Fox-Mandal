<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IssueBook;
use App\Models\Bookshelve;
use App\Models\Book;
use App\Models\BookTransfer;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\BookReturnedNotification;
use Illuminate\Support\Facades\Validator;

class IssueBookController extends Controller
{
    public function store(Request $request)
    {

       
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|array',
            'user_id' => 'required', 
            // 'request_date' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false,'error' => $validator->errors()], 400);
        }

        $insertedData = [];

        try {
            if($request->book_id){
                
            }
            foreach ($request->book_id as $bookId) {
                $data = IssueBook::create([
                    'user_id' => $request->user_id,
                    'book_id' => $bookId,
                    'request_date' => now()->toDateString(),
                ]);
                $insertedData[] = $data;
            }

            return response()->json([
                'status' => true,
                'message' => 'Books issued successfully.',
                'data' => $insertedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while issuing books.',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function listByUser(Request $request)
    {
  
        $userId = $request->input('user_id');

        $books = IssueBook::where('user_id', $userId)->with('book')
            ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'status'=>false,
                'message' => 'No issued books found for this user.'
            ], 404);
        }
        return response()->json([
            'status'=>true,
            'message' => 'List of book of user',
            'data' =>$books
        ], 200);
    }

    public function issuedBookListByUser(Request $request)
    {
      
        $issuedBooks = IssueBook::where('user_id', $request['user_id'])
            ->where('status', 1)
            ->whereNull('is_return')
            ->with('book') 
            ->orderBy('approve_date', 'desc')
            ->get();
            
        if ($issuedBooks->isEmpty()) {
            return response()->json([
                'status'=>false,
                'message' => 'No issued books found for this user.'
            ], 404);
        }

        return response()->json([
            'status'=>true,
            'message' => 'Issued books list.',
            'issued_books' => $issuedBooks
        ], 200);
    }

    public function requestedBookListByUser(Request $request)
    {
        $issuedBooks = IssueBook::where('user_id', $request['user_id'])
            ->whereNull('status')
            ->with('book') 
            ->orderBy('request_date', 'desc')
            ->get();

        if ($issuedBooks->isEmpty()) {
            return response()->json([
                'message' => 'No issued books found for this user.',
            ], 404);
        }

        return response()->json([
            'status'=>true,
            'message' => 'Issued books retrieved successfully.',
            'issued_books' => $issuedBooks
        ], 200);
    }

    public function returnBook(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'qrcode' => 'required|string',          
            'user_id' => 'required',          
            'book_id' => 'required|integer|exists:books,id', 
            
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(),'status' => false], 400);
        }
        $bookshelf = Bookshelve::where('qrcode', $request->qrcode)->with('office')
        ->first();

        if (!$bookshelf) {
            return response()->json(['message' => 'No bookshelf found for the provided QR code.','status' => false], 404);
        }

        $book = Book::where('bookshelves_id', $bookshelf->id)
                    ->where('id', $request->book_id) 
                    ->first();

        if (!$book) {
            return response()->json(['message' => 'No book found on the provided bookshelf.','status' => false], 404);
        }

        $issueBook = IssueBook::where([
            'book_id' => $book->id, 
            'user_id' => $request->user_id,
        ])->first();

        if (!$issueBook) {
            return response()->json(['message' => 'No active issue record found for this book or the book has already been returned.','status' => false], 404);
        }

        $issueBook->update([
            'is_return' => 1,                 
            'return_date' => Carbon::now()->toDateString(), 
        ]);

      
        return response()->json([
            'status'=>true,
            'message' => 'Book return status updated successfully.',
            'data' => $issueBook,
            'shelve_data'=>$bookshelf,
             
        ],200);
    }


    public function transferBook(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'from_user_id' => 'required', 
            'to_user_id' => 'required',   
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false,'error' => $validator->errors()], 400);
        }
   
        $data = BookTransfer::create([
            'is_transfer' => 1,
            'from_user_id' => $request->from_user_id,
            'to_user_id' => $request->to_user_id,
            'transfer_date' => now()->toDateString(),
        ]);
        
        return response()->json([
            'status'=>true,
            'message' => 'Book transfer status updated successfully.',
            'data' => $issueBook
            
        ],200);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to book transfer status updated '
                
            ], 500); 
        }
    }
    
    
    
}
