<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Facility\OfficeController;
use App\Http\Controllers\Lms\BookCategoryController;
use App\Http\Controllers\Lms\BookshelveController;
use App\Http\Controllers\Lms\BookController;
use App\Http\Controllers\Lms\MemberController;
use App\Http\Controllers\Lms\IssueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::group(['middleware' => ['role:super-admin|lms-admin']], function() {
Route::group(['middleware' => ['auth']], function() {

    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);

    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
    Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
    Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);
    
    
    //facility
    
    Route::resource('offices', OfficeController::class);
    Route::get('offices/{userId}/delete', [OfficeController::class, 'destroy']);
    Route::get('offices/{userId}/issue/list', [OfficeController::class, 'bookIssueList']);
    Route::get('offices/{userId}/available/list', [OfficeController::class, 'availableBookList']);
    
    //lms
    Route::resource('bookcategories', BookCategoryController::class);
    Route::get('bookcategories/{userId}/delete', [BookCategoryController::class, 'destroy']);
    
    Route::resource('bookshelves', BookshelveController::class);
    Route::get('bookshelves/{userId}/delete', [BookshelveController::class, 'destroy']);
    Route::get('bookshelves/export/csv', [BookshelveController::class, 'csvExport']);
    Route::post('bookshelves/upload/csv', [BookshelveController::class, 'csvImport']);
    
    
    Route::resource('books', BookController::class);
    Route::get('books/{userId}/delete', [BookController::class, 'destroy']);
    Route::get('books/{userId}/status/change', [BookController::class, 'status']);
    Route::get('books/export/csv', [BookController::class, 'csvExport']);
    Route::post('books/upload/csv', [BookController::class, 'csvImport']);
    Route::get('bookshelves/list/officewise/{userId}', [BookController::class, 'bookshelveOffice']);
    Route::get('books/{userId}/issue/list', [BookController::class, 'bookIssueList']);
    Route::get('books/issue/list/export/csv', [BookController::class, 'bookIssuecsvExport']);
    Route::get('bookshelves/get/{userId}', [BookController::class, 'bookshelveDetail']);
    //total available books per office
    Route::get('offices/available/books/{officeId}/list', [BookController::class, 'availableBookListOffice']);
    //total issue books per office
    Route::get('offices/issue/books/{officeId}/list', [BookController::class, 'issueBookListOffice']);
    
    //unreturned book list
    Route::get('unreturned/books/list', [BookController::class, 'unreturnedBookList']);
    Route::get('unreturned/books/export/csv', [BookController::class, 'unreturnedBookcsvExport']);
    //member
    Route::resource('members', MemberController::class);
    Route::get('members/{userId}/delete', [MemberController::class, 'destroy']);
    Route::get('members/{userId}/status/change', [MemberController::class, 'status']);
    Route::get('members/{userId}/issue/list', [MemberController::class, 'bookIssueList']);
    Route::get('members/issue/list/csv/export', [MemberController::class, 'bookIssueListcsvExport']);
    Route::get('members/export/csv', [MemberController::class, 'csvExport']);
    Route::post('members/upload/csv', [MemberController::class, 'csvImport']);
    //all issue list
    Route::resource('issues', IssueController::class);
    Route::get('issues/books/{userId}/status/change', [IssueController::class, 'status']);
});
