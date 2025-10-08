<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/floors-by-branch/{branch_id}', [FloorController::class, 'getByBranch']);
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/registration', [BookingController::class, 'registration']);
    Route::post('/transfer', [BookingController::class, 'transfer']);
    Route::post('/extension', [BookingController::class, 'extension']);
});

require __DIR__ . '/api_auth.php';
