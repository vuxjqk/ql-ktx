<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomAssignmentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomRegistrationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
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
        // Quản lý nhân sự
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{id}', [UserController::class, 'restore'])->name('users.restore');

        // Quản lý chi nhánh
        Route::resource('branches', BranchController::class)->except(['create', 'show', 'edit']);

        // Quản lý phòng
        Route::resource('rooms', RoomController::class)->except(['index', 'show']);
    });

    Route::middleware(['role:super_admin,admin,staff'])->group(function () {
        // Quản lý sinh viên
        Route::resource('students', StudentController::class)->parameters(['students' => 'user']);
        Route::post('/students/{id}', [StudentController::class, 'restore'])->name('students.restore');

        // Quản lý phòng
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

        Route::put('/room_registrations/{roomRegistration}', [RoomRegistrationController::class, 'update'])->name('room_registrations.update');

        Route::get('/students/{user}/room_assignments', [RoomAssignmentController::class, 'index'])->name('room_assignments.index');
        Route::get('/students/{user}/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'show'])->name('room_assignments.show');
        Route::delete('/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'destroy'])->name('room_assignments.destroy');

        Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
        Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');

        Route::get('/rooms/{room}/utilities', [UtilityController::class, 'create'])->name('utilities.create');
        Route::post('/rooms/{room}/utilities', [UtilityController::class, 'store'])->name('utilities.store');
        Route::put('/rooms/{utility}/utilities', [UtilityController::class, 'update'])->name('utilities.update');
        Route::delete('/rooms/{utility}/utilities', [UtilityController::class, 'destroy'])->name('utilities.destroy');
    });

    Route::prefix('student')->middleware(['role:student'])->group(function () {
        Route::get('/room_registrations/create', [RoomRegistrationController::class, 'create'])->name('room_registrations.create');
        Route::post('/room_registrations', [RoomRegistrationController::class, 'store'])->name('room_registrations.store');
        Route::delete('/room_registrations/{roomRegistration}', [RoomRegistrationController::class, 'destroy'])->name('room_registrations.destroy');

        Route::get('/room_assignments/{roomAssignment}/edit', [RoomAssignmentController::class, 'edit'])->name('room_assignments.edit');
        Route::put('/room_assignments/{roomAssignment}', [RoomAssignmentController::class, 'update'])->name('room_assignments.update');

        Route::get('/vnpay/{bill}', [BillController::class, 'redirect'])->name('vnpay.redirect');

        Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    });

    Route::get('/vnpay/callback', [BillController::class, 'callback'])->name('vnpay.callback');
});

require __DIR__ . '/auth.php';
