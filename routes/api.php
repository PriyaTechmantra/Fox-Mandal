<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\IssueBookController;
use App\Http\Controllers\Api\BookShelveController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\BookTransferController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Fms\CabBookingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('login', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/books/search', [BookController::class, 'search']);
Route::get('/books/list', [BookController::class, 'index']);
Route::get('/books/list/with-issuedbook', [BookController::class, 'bookWithIssuedBook']);
Route::get('/books/detail', [BookController::class, 'bookDetails']);
Route::get('/books/details-by-qrcode', [BookController::class, 'searchDetailsByQrCode']);
Route::get('/books/category-wise-list', [BookController::class, 'CategoryWiseBookList']);


Route::middleware('api')->post('/issue-books', [IssueBookController::class, 'store']);

Route::get('/issue-books/list-by-user', [IssueBookController::class, 'listByUser']);
Route::get('/issue-books/issued-list-by-user', [IssueBookController::class, 'issuedBookListByUser']);
Route::get('/issue-books/request-list-by-user', [IssueBookController::class, 'requestedBookListByUser']);

Route::patch('/return-book', [IssueBookController::class, 'returnBook']);
Route::post('/transfer-book', [BookTransferController::class, 'transferBook']);

Route::get('/books-shelve/search-by-qrcode', [BookShelveController::class, 'searchByQrCode']);
Route::post('/bookmark', [BookmarkController::class, 'store']);
Route::get('/bookmark/list', [BookmarkController::class, 'index']);

Route::get('/books/detail-by-book-shelves-qrcode', [BookController::class, 'showBooksByBookShelveQRCode']);
Route::get('/books/detail-by-book-shelves', [BookController::class, 'showBooksByBookShelve']);


Route::post('/save-fcm-token', [NotificationController::class, 'saveToken']);


Route::post('/save-notification', [NotificationController::class, 'Notification']);
Route::get('/notification-list-by-user', [NotificationController::class, 'notificationListByUser']);
Route::post('/notification-read', [NotificationController::class, 'markAsRead']);


Route::prefix('cab_bookings')->group(function () {
    // Route::get('/', [BookingController::class, 'index']);            
    // Route::get('/{id}', [BookingController::class, 'show']);         
    Route::post('/store', [CabBookingController::class, 'store']);           
    Route::put('/{id}', [CabBookingController::class, 'update']);      
    Route::delete('/{id}', [CabBookingController::class, 'destroy']);  
});






