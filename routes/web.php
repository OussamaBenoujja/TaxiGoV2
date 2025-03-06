<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/test', function () {
    return "Admin test page - no middleware";
});
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard-test', function() {
        return "If you see this, the admin middleware allowed you through!";
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
});
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/bookings', [App\Http\Controllers\AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/drivers', [App\Http\Controllers\AdminController::class, 'drivers'])->name('admin.drivers');
    Route::get('/statistics', [App\Http\Controllers\AdminController::class, 'statistics'])->name('admin.statistics');
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

// Payment routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/bookings/{booking}/payment', [App\Http\Controllers\PaymentController::class, 'showPaymentForm'])
        ->name('bookings.payment.form');
    Route::post('/bookings/{booking}/payment/intent', [App\Http\Controllers\PaymentController::class, 'createPaymentIntent'])
        ->name('bookings.payment.intent');   
    Route::post('/bookings/{booking}/payment/complete', [App\Http\Controllers\PaymentController::class, 'markPaymentComplete'])
        ->name('bookings.payment.complete');
});


Route::post('/stripe/webhook', [App\Http\Controllers\PaymentController::class, 'handleWebhook'])
    ->name('stripe.webhook');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/bookings/{bookingId}/review', [App\Http\Controllers\ReviewController::class, 'create'])
        ->name('reviews.create');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])
        ->name('reviews.store');
    Route::get('/reviews/{id}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])
        ->name('reviews.edit');
    Route::put('/reviews/{id}', [App\Http\Controllers\ReviewController::class, 'update'])
        ->name('reviews.update');
    Route::delete('/reviews/{id}', [App\Http\Controllers\ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
    Route::get('/users/{userId}/reviews', [App\Http\Controllers\ReviewController::class, 'showUserReviews'])
        ->name('reviews.user');

});


Route::get('/profiles/{userId}', [App\Http\Controllers\PublicProfileController::class, 'show'])
    ->name('profiles.public');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/qr-code', [App\Http\Controllers\QRCodeController::class, 'showProfileQR'])
        ->name('profile.qr-code');
    Route::get('/profile/qr-code/{userId}', [App\Http\Controllers\QRCodeController::class, 'showProfileQR'])
        ->name('profile.qr-code.user');
});
require __DIR__.'/auth.php';
