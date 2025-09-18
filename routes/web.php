<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{id}', [UserController::class, 'restore'])->name('users.restore');
    });

    Route::middleware(['role:super_admin,admin,staff'])->group(function () {
        Route::resource('students', StudentController::class)->except(['show'])->parameters(['students' => 'user']);
        Route::post('/students/{id}', [StudentController::class, 'restore'])->name('students.restore');
    });
});

require __DIR__ . '/auth.php';
