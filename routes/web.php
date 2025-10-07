<?php

use App\Http\Controllers\Api\RoomController as ApiRoomController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
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

    Route::prefix('admin')->group(function () {
        Route::middleware(['role:super_admin,admin'])->group(function () {
            Route::put('users/{user}/branches', [UserController::class, 'updateBranches'])->name('users.updateBranches');
            Route::resource('users', UserController::class)->except(['show']);
            Route::post('branches/{branch}/floors', [FloorController::class, 'store'])->name('floors.store');
            Route::put('floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
            Route::delete('floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');
            Route::resource('branches', BranchController::class)->except(['create', 'show', 'edit']);
            Route::resource('services', ServiceController::class)->except(['create', 'show', 'edit']);
        });

        Route::middleware(['role:super_admin,admin,staff'])->group(function () {
            Route::resource('students', StudentController::class)->parameters(['students' => 'user']);
            Route::post('rooms/{room}/images', [RoomController::class, 'storeImages'])->name('rooms.storeImages');
            Route::delete('rooms/{image}/images', [RoomController::class, 'destroyImage'])->name('rooms.destroyImage');
            Route::put('rooms/{room}/services', [RoomController::class, 'updateServices'])->name('rooms.updateServices');
            Route::resource('rooms', RoomController::class)->except(['show']);
        });
    });

    Route::get('/floors-by-branch/{branch_id}', [FloorController::class, 'getByBranch']);
    Route::get('/rooms', [ApiRoomController::class, 'index']);
    Route::get('/rooms/{room}', [ApiRoomController::class, 'show']);
});

require __DIR__ . '/auth.php';
