<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceUsageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['vi', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/chatbot', [ChatbotController::class, 'handleChat'])->name('chatbot.handle');
Route::get('/floors-by-branch/{branchId}', [FloorController::class, 'getByBranch']);

Route::middleware(['auth', 'verified', 'branch'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/dormitory', [SettingController::class, 'dormitory'])->name('settings.dormitory');
    Route::put('/settings/security', [SettingController::class, 'security'])->name('settings.security');

    Route::get('/backup', [BackupController::class, 'index'])->name('backup');
    Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');
    Route::get('/backup/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::put('/users/{user}/branches', [UserController::class, 'updateBranches'])->name('users.updateBranches');
        Route::resource('/users', UserController::class)->except(['show']);
        Route::post('/branches/{branch}/floors', [FloorController::class, 'store'])->name('floors.store');
        Route::put('/floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
        Route::delete('/floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');
        Route::resource('/branches', BranchController::class)->except(['create', 'show', 'edit']);
        Route::resource('/services', ServiceController::class)->except(['create', 'show', 'edit']);
        Route::resource('/amenities', AmenityController::class)->except(['create', 'show', 'edit']);
    });

    Route::middleware(['role:super_admin,admin,staff'])->group(function () {
        Route::post('/bills/{bill}/refund', [RefundController::class, 'store'])->name('bills.refund');
        Route::get('/bills/{bill}/export', [BillController::class, 'export'])->name('bills.export');
        Route::post('/bills/{bill}/pay', [BillController::class, 'payBill'])->name('bills.pay');
        Route::post('/bills/{bill}/cancel-bills', [BillController::class, 'cancelBills'])->name('bills.cancelBills');
        Route::get('/students/{user}/bills', [BillController::class, 'index'])->name('bills.index');
        Route::post('/students/{user}/bills', [BillController::class, 'store'])->name('bills.store');
        Route::get('/students/{user}/bills/create', [BillController::class, 'create'])->name('bills.create');
        Route::resource('/students', StudentController::class)->parameters(['students' => 'user']);
        Route::get('/rooms/{room}/service-usages', [ServiceUsageController::class, 'index'])->name('service-usages.index');
        Route::put('/rooms/{room}/service-usages', [ServiceUsageController::class, 'update'])->name('service-usages.update');
        Route::post('/rooms/{room}/images', [RoomController::class, 'storeImages'])->name('rooms.storeImages');
        Route::delete('/rooms/{image}/images', [RoomController::class, 'destroyImage'])->name('rooms.destroyImage');
        Route::put('/rooms/{room}/services', [RoomController::class, 'updateServices'])->name('rooms.updateServices');
        Route::put('/rooms/{room}/amenities', [RoomController::class, 'updateAmenities'])->name('rooms.updateAmenities');
        Route::resource('/rooms', RoomController::class);
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::put('/bookings/{booking}/terminate', [BookingController::class, 'terminateBooking'])->name('bookings.terminateBooking');
        Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
        Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');
        Route::post('/payments/{booking}', [PaymentController::class, 'store'])->name('payments.store');
        Route::resource('/notifications', NotificationController::class)->except(['create', 'show', 'edit', 'update']);
    });
});

require __DIR__ . '/auth.php';
