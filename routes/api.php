<?php

use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RepairController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bills/list', [BillController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::delete('/profile', [ProfileController::class, 'deleteAccount']);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/show', [BookingController::class, 'show']);
    Route::post('/bookings/book', [BookingController::class, 'book']);
    Route::post('/bookings/transfer/{book}', [BookingController::class, 'transfer']);
    Route::post('/bookings/extend/{book}', [BookingController::class, 'extend']);

    Route::post('/repairs', [RepairController::class, 'store']);
    Route::delete('/repairs/{repair}', [RepairController::class, 'destroy']);

    Route::post('/favourites/{roomId}', [FavouriteController::class, 'add']);
    Route::delete('/favourites/{roomId}', [FavouriteController::class, 'remove']);

    Route::post('/reviews/{roomId}', [ReviewController::class, 'upsert']);
});

Route::post('/vnpay/redirect/{bill}', [PaymentController::class, 'redirect']);
Route::get('/vnpay/callback', [PaymentController::class, 'callback']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);
Route::get('/rooms/{room}/reviews', [ReviewController::class, 'index']);
Route::post('/rooms/{room}/reviews', [ReviewController::class, 'store']);
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

Route::get('/branches', [BranchController::class, 'index']);
Route::get('/amenities', [AmenityController::class, 'index']);

Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

Route::get('/favourites', [FavouriteController::class, 'index']);
Route::post('/favourites', [FavouriteController::class, 'store']);
Route::delete('/favourites/{room}', [FavouriteController::class, 'destroy']);
