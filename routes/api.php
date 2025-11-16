<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    //
});

Route::post('/vnpay/redirect/{bill}', [PaymentController::class, 'redirect']);
Route::get('/vnpay/callback', [PaymentController::class, 'callback']);
