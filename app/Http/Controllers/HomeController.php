<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Auth;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = (object)[];
        if(!empty(Auth::user()->office_id)){
             $booksPerOffice = DB::table('books')->join('offices', 'offices.id', '=', 'books.office_id')
                                ->select('offices.name','offices.address','offices.id', DB::raw('count(*) as total_books'))
                                ->where('books.office_id',Auth::user()->office_id)
                                ->groupBy('books.office_id')
                                ->get();

            $booksPerShelf = DB::table('books')->join('bookshelves', 'bookshelves.id', '=', 'books.bookshelves_id')
                            ->select('bookshelves.number','bookshelves.id', DB::raw('count(*) as total_books'))
                            ->where('books.office_id',Auth::user()->office_id)
                            ->groupBy('books.bookshelves_id')
                            ->get();
            $issuedBooksPerOffice = DB::table('issue_books')
                                    ->join('books', 'issue_books.book_id', '=', 'books.id')
                                    ->join('offices', 'offices.id', '=', 'books.office_id')
                                    ->select('books.office_id','offices.name','offices.address', DB::raw('count(issue_books.id) as total_issued'))
                                    ->where(function($query) {
                                        $query->whereNull('issue_books.is_return')
                                             ->orWhere('issue_books.status',1);
                                    })
                                    
                                    ->where('books.office_id',Auth::user()->office_id)
                                   
                                    ->groupBy('books.office_id')
                                    ->get();
            $availableBooksPerOffice = DB::table('books')
                                        ->leftJoin('issue_books', 'books.id', '=', 'issue_books.book_id')
                                        ->join('offices', 'offices.id', '=', 'books.office_id')
                                        ->select('books.office_id','offices.name','offices.id','offices.address', DB::raw('count(books.id) as total_available'))
                                        ->where('books.office_id',Auth::user()->office_id)
                                        ->where(function($query) {
                                            $query->whereNull('issue_books.book_id')
                                              ->orWhere('issue_books.is_return',1)->orWhere('issue_books.status',0);
                                        })
                                       
                                        ->groupBy('books.office_id')
                                        ->get();
        }else{
            $booksPerOffice = DB::table('books')->join('offices', 'offices.id', '=', 'books.office_id')
                                ->select('offices.name','offices.address','offices.id', DB::raw('count(*) as total_books'))
                                ->groupBy('books.office_id')
                                ->get();

            $booksPerShelf = DB::table('books')->join('bookshelves', 'bookshelves.id', '=', 'books.bookshelves_id')
                            ->select('bookshelves.number','bookshelves.id', DB::raw('count(*) as total_books'))
                            ->groupBy('books.bookshelves_id')
                            ->get();
            $issuedBooksPerOffice = DB::table('issue_books')
                                    ->join('books', 'issue_books.book_id', '=', 'books.id')
                                    ->join('offices', 'offices.id', '=', 'books.office_id')
                                    ->select('books.office_id','offices.name','offices.address', DB::raw('count(issue_books.id) as total_issued'))
                                    ->where(function($query) {
                                        $query->whereNull('issue_books.is_return')
                                             ->orWhere('issue_books.status',1);
                                    })
                                   
                                    
                                    ->groupBy('books.office_id')
                                    ->get();
            $availableBooksPerOffice = DB::table('books')
                                        ->leftJoin('issue_books', 'books.id', '=', 'issue_books.book_id')
                                        ->join('offices', 'offices.id', '=', 'books.office_id')
                                        ->select('books.office_id','offices.name','offices.id','offices.address', DB::raw('count(books.id) as total_available'))
                                         ->where(function($query) {
                                            $query->whereNull('issue_books.book_id')
                                              ->orWhere('issue_books.is_return',1)->orWhere('issue_books.status',0);
                                        })
                                       
                                        ->groupBy('books.office_id')
                                        ->get();
        }
       
        return view('home',compact('booksPerOffice','booksPerShelf','issuedBooksPerOffice','availableBooksPerOffice'));
    }
}
