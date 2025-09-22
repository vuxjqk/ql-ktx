<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomAssignmentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomRegistrationController;
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
        Route::resource('rooms', RoomController::class)->except(['show']);
    });

    Route::middleware(['role:super_admin,admin,staff'])->group(function () {
        Route::resource('students', StudentController::class)->except(['show'])->parameters(['students' => 'user']);
        Route::post('/students/{id}', [StudentController::class, 'restore'])->name('students.restore');

        Route::get('/room_registrations', [RoomRegistrationController::class, 'index'])->name('room_registrations.index');
        Route::get('/room_registrations/{roomRegistration}', [RoomRegistrationController::class, 'show'])->name('room_registrations.show');
        Route::put('/room_registrations/{roomRegistration}', [RoomRegistrationController::class, 'update'])->name('room_registrations.update');

        Route::get('/room_assignments', [RoomAssignmentController::class, 'index'])->name('room_assignments.index');
        Route::get('/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'show'])->name('room_assignments.show');
        Route::delete('/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'destroy'])->name('room_assignments.destroy');

        Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');
    });

    Route::prefix('student')->middleware(['role:student'])->group(function () {
        Route::get('/room_registrations/create', [RoomRegistrationController::class, 'create'])->name('room_registrations.create');
        Route::post('/room_registrations', [RoomRegistrationController::class, 'store'])->name('room_registrations.store');
        Route::delete('/room_registrations/{roomRegistration}', [RoomRegistrationController::class, 'destroy'])->name('room_registrations.destroy');

        Route::get('/room_assignments/{roomAssignment}/edit', [RoomAssignmentController::class, 'edit'])->name('room_assignments.edit');
        Route::put('/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'update'])->name('room_assignments.update');

        Route::get('/vnpay/{bill}', [BillController::class, 'redirect'])->name('vnpay.redirect');
    });

    Route::get('/vnpay/callback', [BillController::class, 'callback'])->name('vnpay.callback');
});

require __DIR__ . '/auth.php';
