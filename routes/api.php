<?php

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

require __DIR__ . '/api_auth.php';
