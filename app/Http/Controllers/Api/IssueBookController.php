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
    public function bulkBookIssueWithQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qrcode' => 'required|array', 
            'user_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }
    
        $insertedData = [];
    
        try {
            foreach ($request->qrcode as $qrcode) {
                $book = Book::where('qrcode', $qrcode)->first();
    
                if (!$book) {
                    return response()->json([
                        'status' => false,
                        'message' => "Book with QR code $qrcode not found."
                    ], 404);
                }
    
                $data = IssueBook::create([
                    'user_id' => $request->user_id,
                    'book_id' => $book->id,
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
    

   

    public function singleBookIssueWithQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qrcode' => 'required|string', 
            'user_id' => 'required',
            'book_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        try {
            $book = Book::where('qrcode', $request->qrcode)->first();

            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => "Book with QR code {$request->qrcode} not found."
                ], 404);
            }

            if ($book->id != $request->book_id) {
                return response()->json([
                    'status' => false,
                    'message' => "The QR code does not match the provided book ID."
                ], 400);
            }

            $data = IssueBook::create([
                'user_id' => $request->user_id,
                'book_id' => $book->id,
                'request_date' => now()->toDateString(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Book issued successfully.',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while issuing the book.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function issueBookForAnotherUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qrcode' => 'required|string',  
            'user_id' => 'required|exists:users,id',  
           // 'book_holder_user_id' => 'required|exists:users,id', 
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }
    
        try {
            $book = Book::where('qrcode', $request->qrcode)->first();
    
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => "Book with QR code {$request->qrcode} not found."
                ], 404);
            }
    
            $bookHolder = User::find($request->user_id2);
    
            $data = IssueBook::create([
                'user_id' => $request->user_id,  
                'book_id' => $book->id,
                'request_date' => now()->toDateString(),
                'user_id2' => $bookHolder->id,  
                'name_of_issue_person' => $request->name_of_issue_person,  
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Book issued successfully to the user.',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while issuing the book.',
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
    
    public function transferBookByQr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',  
            'from_user_id' => 'required',
            'to_user_id' => 'required',  
            'qrcode' => 'required',  
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }
    
        $book = Book::where('id', $request->book_id)
                    ->where('qrcode', $request->qrcode)
                    ->first();

        
        if (!$book) {
            return response()->json([
                'status' => false,
                'message' => 'Book not found.',
            ], 404);
        }

        
        $issuebook = IssueBook::where('id', $request->book_id)
        ->where('user_id', $request->from_user_id)
        ->first();

        if ( ! $issuebook) {
            return response()->json([
                'status' => false,
                'message' => 'The specified user does not currently hold this book.',
            ], 403);
        }
    
        $data = BookTransfer::create([
            'book_id' => $book->id,
            'from_user_id' => $request->from_user_id,
            'to_user_id' => $request->to_user_id,
            'is_transfer' => 1,
            'transfer_date' => now()->toDateString(),
        ]);
    
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update book transfer status.',
            ], 500);
        }
    
        
    
        return response()->json([
            'status' => true,
            'message' => 'Book transfer status updated successfully.',
            'data' => $data,
        ], 200);
    }

    
    public function searchByCategory(Request $request)
    {
    

        $userId = $request->user_id; 
        $keyword = $request->input('keyword');

        
        if (empty($keyword)) {
            $books = IssueBook::where('user_id', $userId)
                ->with(['book.category', 'user'])
                ->get();
            
            if ($books->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No issued books found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'All issued books retrieved successfully.',
                'data' => $books
            ], 200);
        }

        $books = IssueBook::where('user_id', $userId)
            ->whereHas('book.category', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%'); 
            })
            ->with(['book.category', 'user'])
            ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No issued books found for the selected category.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Books retrieved successfully.',
            'data' => $books
        ], 200);
    }


    
}
