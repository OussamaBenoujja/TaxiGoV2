<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/book-taxi', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/book-taxi', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/become-driver', [ProfileController::class, 'showDriverRegistration'])
    ->name('become.driver');
    Route::post('/become-driver', [ProfileController::class, 'registerAsDriver'])
        ->name('register.as.driver');

});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::post('/bookings/{bookingId}/update-status', [BookingController::class, 'updateStatus'])
    ->name('bookings.update-status');


use App\Http\Controllers\MessageController;
Broadcast::routes();
Broadcast::routes(['middleware' => ['web', 'auth']]);
Route::middleware(['auth'])->group(function () {
        Route::get('/bookings/{bookingId}/chat', [MessageController::class, 'index'])->name('bookings.chat');
        Route::post('/bookings/{bookingId}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    });

Route::get('/test-auth', function () {
    return [
        'auth_route' => '/broadcasting/auth',
        'route_exists' => true
    ];
});

require __DIR__.'/auth.php';
