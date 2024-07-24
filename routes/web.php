<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('registration');
});

//User

Route::get('user-registration', [UserController::class,'index'])->name('user.register');

Route::post('user-store', [UserController::class,'userPostRegistration'])->name('user.postregistration');

Route::get('user-login', [UserController::class,'userLoginIndex'])->name('user.login');

Route::post('login', [UserController::class,'userPostLogin'])->name('user.postlogin');

Route::get('logout', [UserController::class,'logout'])->name('user.logout');


Route::middleware(['auth'])->group(function () {
    // BookCategory
    Route::get('/book-category', [BookCategoryController::class, 'showCreateForm'])->name('bookCategory.create');
    Route::post('/book-category/create', [BookCategoryController::class, 'bookCategoryCreate'])->name('bookCategory.store');
    Route::get('/book-category/{id}/edit', [BookCategoryController::class, 'edit'])->name('bookCategory.edit');
    Route::put('/book-category/{id}/update', [BookCategoryController::class, 'update'])->name('bookCategory.update');
    Route::delete('/book-category/{id}/delete', [BookCategoryController::class, 'destroy'])->name('bookCategory.destroy');

    // Books
    Route::get('/dashboard', [BookController::class, 'index'])->name('dashboard');
    Route::get('/books', [BookController::class, 'index'])->name('books.index'); // Route for listing books
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');


    Route::get('/books/filter', [BookController::class, 'filter'])->name('books.filter');
    
});