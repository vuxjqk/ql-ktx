<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RepairController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public browse
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/{notification}', [NotificationController::class, 'show']);

        // Student + staff access
        Route::middleware('api.role:student,staff,admin,super_admin')->group(function () {
            // Bookings
            Route::post('/bookings', [BookingController::class, 'store']);
            Route::get('/bookings/my', [BookingController::class, 'myBookings']);
            Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

            // Rooms favourites
            Route::post('/rooms/{room}/favourite', [RoomController::class, 'toggleFavourite']);
            Route::get('/rooms/favourites/my', [RoomController::class, 'myFavourites']);

            // Bills
            Route::get('/bills/my', [BillController::class, 'myBills']);
            Route::get('/bills/{bill}', [BillController::class, 'show']);

            // Repairs
            Route::post('/repairs', [RepairController::class, 'store']);
            Route::get('/repairs/my', [RepairController::class, 'myRepairs']);
            Route::delete('/repairs/{repair}', [RepairController::class, 'destroy']);

            // Reviews
            Route::post('/rooms/{room}/reviews', [RoomController::class, 'addReview']);
        });

        // Staff/Admin tools
        Route::middleware('api.role:staff,admin,super_admin')->group(function () {
            Route::post('/rooms', [RoomController::class, 'store']);
            Route::put('/rooms/{id}', [RoomController::class, 'update']);
            Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);

            Route::get('/bookings', [BookingController::class, 'index']);
            Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);

            Route::post('/bills', [BillController::class, 'store']);
            Route::put('/bills/{id}/status', [BillController::class, 'updateStatus']);

            Route::get('/repairs', [RepairController::class, 'index']);
            Route::put('/repairs/{id}', [RepairController::class, 'update']);
        });
    });
});

Route::post('/vnpay/redirect/{bill}', [PaymentController::class, 'redirect']);
Route::get('/vnpay/callback', [PaymentController::class, 'callback']);
