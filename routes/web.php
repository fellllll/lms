<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/book', [BookController::class, 'show'])->name('book.show');
    Route::get('/book/{id}', [BookController::class, 'detail'])->name('book.detail');
  
    Route::get('/reserve/{id}', [ReservationController::class, 'show'])->name('reserve.show');
    Route::post('reserve', [ReservationController::class, 'submit'])->name('reserve.submit');
    Route::get('reservation', [ReservationController::class, 'view'])->name('reserve.view');
    Route::delete('/reserve/{id}', [ReservationController::class, 'destroy'])->name('reserve.destroy');

    Route::middleware(['role:1'])->group(function () {
        Route::get('/book/edit/{book:id}', [BookController::class, 'edit'])->name('book.edit');
        Route::put('/book/update/{book:id}', [BookController::class, 'update'])->name('book.update');
        Route::delete('/book/delete/{book:id}', [BookController::class, 'destroy'])->name('book.destroy');
        Route::get('/books', [BookController::class, 'list'])->name('book.list');
        Route::get('/books/add', [BookController::class, 'add'])->name('book.add');
        Route::post('/books/submit', [BookController::class, 'submit'])->name('book.submit');
        Route::get('/reservation/edit/{reservation:id}', [ReservationController::class, 'edit'])->name('reservation.edit');
        Route::put('/reservation/update/{reservation:id}', [ReservationController::class, 'update'])->name('reservation.update');
    });    
});

require __DIR__.'/auth.php';