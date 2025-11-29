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
use App\Http\Controllers\Student\BookingController as StudentBookingController;
use App\Http\Controllers\Student\ContactController;
use App\Http\Controllers\Student\FavouriteController;
use App\Http\Controllers\Student\HomeController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\RepairController as StudentRepairController;
use App\Http\Controllers\Student\ReviewController;
use App\Http\Controllers\Student\RoomController as StudentRoomController;
use App\Http\Controllers\Student\ServiceCostController;
use App\Http\Controllers\Student\SocialiteController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/

// Đổi ngôn ngữ
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['vi', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Chatbot công khai
Route::post('/chatbot', [ChatbotController::class, 'handleChat'])->name('chatbot.handle');

// Lấy danh sách tầng theo chi nhánh (dùng cho AJAX)
Route::get('/floors-by-branch/{branchId}', [FloorController::class, 'getByBranch']);

/*
|--------------------------------------------------------------------------
| Student Public Area (Giao diện sinh viên - không cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('student.home');
Route::get('/about', fn() => view('student.about'))->name('student.about');

Route::get('/contact', [ContactController::class, 'create'])->name('student.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('student.contact.store');

// Social Login
Route::get('/auth/{provider}', [SocialiteController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| Student Protected Area (Prefix: /student, Name: student.*)
|--------------------------------------------------------------------------
*/

Route::prefix('student')->name('student.')->group(function () {

    // Danh sách & chi tiết phòng (công khai cho sinh viên xem)
    Route::get('/rooms', [StudentRoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [StudentRoomController::class, 'show'])->name('rooms.show');

    // Yêu cầu đăng nhập + xác thực email
    Route::middleware(['auth', 'verified'])->group(function () {

        // Yêu thích phòng
        Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
        Route::post('/favourites/{room}', [FavouriteController::class, 'toggleFavourite'])->name('favourites.toggleFavourite');

        // Đánh giá phòng
        Route::post('/reviews/{room}', [ReviewController::class, 'reviewRoom'])->name('reviews.reviewRoom');

        // Đặt phòng & quản lý booking
        Route::get('/bookings', [StudentBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{room}', [StudentBookingController::class, 'store'])->name('bookings.store');
        Route::patch('/bookings/{booking}', [StudentBookingController::class, 'terminate'])->name('bookings.terminate');
        Route::post('/bookings/{booking}/extend', [StudentBookingController::class, 'extend'])->name('bookings.extend');
        Route::delete('/bookings/{booking}', [StudentBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/bookings/history', [StudentBookingController::class, 'history'])->name('bookings.history');

        // Báo sửa chữa
        Route::post('/repairs', [StudentRepairController::class, 'store'])->name('repairs.store');

        // Xem bảng giá dịch vụ
        Route::get('/service-costs', [ServiceCostController::class, 'index'])->name('service-costs.index');

        // Thanh toán hóa đơn
        Route::get('/payments/{bill}', [StudentPaymentController::class, 'store'])->name('payments.store');

        // VNPay
        Route::get('/vnpay/redirect/{bill}', [StudentPaymentController::class, 'redirect'])->name('vnpay.redirect');
        Route::get('/vnpay/callback', [StudentPaymentController::class, 'callback'])->name('vnpay.callback');

        // ZaloPay
        Route::get('/zalopay/redirect/{bill}', [StudentPaymentController::class, 'redirectZaloPay'])->name('zalopay.redirect');
        Route::get('/zalopay/return', [StudentPaymentController::class, 'returnZaloPay'])->name('zalopay.return');

        // Hồ sơ cá nhân
        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('password.update');
        Route::patch('/profile/avatar', [StudentProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::delete('/profile', [StudentProfileController::class, 'destroy'])->name('profile.destroy');

        // Thông báo
        Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [StudentNotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/read', [StudentNotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::post('/notifications/mark-all-read', [StudentNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    });
});

/*
|--------------------------------------------------------------------------
| Admin/Staff Protected Area (Yêu cầu đăng nhập + chọn chi nhánh)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'branch'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard & Thống kê
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    /*
    |----------------------------------------------------------------------
    | Cài đặt hệ thống
    |----------------------------------------------------------------------
    */
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/dormitory', [SettingController::class, 'dormitory'])->name('settings.dormitory');
    Route::put('/settings/security', [SettingController::class, 'security'])->name('settings.security');

    /*
    |----------------------------------------------------------------------
    | Sao lưu dữ liệu
    |----------------------------------------------------------------------
    */
    Route::get('/backup', [BackupController::class, 'index'])->name('backup');
    Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');
    Route::get('/backup/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');

    /*
    |----------------------------------------------------------------------
    | Hồ sơ cá nhân (nhân viên)
    |----------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |----------------------------------------------------------------------
    | Quản lý người dùng & phân quyền (Chỉ Super Admin + Admin)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin'])->group(function () {

        // Gán chi nhánh cho user
        Route::put('/users/{user}/branches', [UserController::class, 'updateBranches'])->name('users.updateBranches');

        // Quản lý tài khoản nhân viên
        Route::resource('/users', UserController::class)->except(['show']);

        // Quản lý chi nhánh
        Route::resource('/branches', BranchController::class)->except(['create', 'show', 'edit']);

        // Quản lý tầng
        Route::post('/branches/{branch}/floors', [FloorController::class, 'store'])->name('floors.store');
        Route::put('/floors/{floor}', [FloorController::class, 'update'])->name('floors.update');
        Route::delete('/floors/{floor}', [FloorController::class, 'destroy'])->name('floors.destroy');

        // Quản lý dịch vụ & tiện ích
        Route::resource('/services', ServiceController::class)->except(['create', 'show', 'edit']);
        Route::resource('/amenities', AmenityController::class)->except(['create', 'show', 'edit']);
    });

    /*
    |----------------------------------------------------------------------
    | Quản lý hoạt động ký túc xá (Super Admin + Admin + Staff)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin,admin,staff'])->group(function () {

        // Quản lý sinh viên
        Route::resource('/students', StudentController::class)->parameters(['students' => 'user']);

        // Quản lý phòng
        Route::resource('/rooms', RoomController::class);
        Route::post('/rooms/{room}/images', [RoomController::class, 'storeImages'])->name('rooms.storeImages');
        Route::delete('/rooms/{image}/images', [RoomController::class, 'destroyImage'])->name('rooms.destroyImage');
        Route::put('/rooms/{room}/services', [RoomController::class, 'updateServices'])->name('rooms.updateServices');
        Route::put('/rooms/{room}/amenities', [RoomController::class, 'updateAmenities'])->name('rooms.updateAmenities');

        // Dịch vụ sử dụng trong phòng
        Route::get('/rooms/{room}/service-usages', [ServiceUsageController::class, 'index'])->name('service-usages.index');
        Route::put('/rooms/{room}/service-usages', [ServiceUsageController::class, 'update'])->name('service-usages.update');

        // Đặt phòng (admin xem & xử lý)
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::put('/bookings/{booking}/terminate', [BookingController::class, 'terminateBooking'])->name('bookings.terminateBooking');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

        // Báo sửa chữa (xử lý)
        Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
        Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');

        // Hóa đơn & thanh toán
        Route::get('/students/{user}/bills', [BillController::class, 'index'])->name('bills.index');
        Route::get('/students/{user}/bills/create', [BillController::class, 'create'])->name('bills.create');
        Route::post('/students/{user}/bills', [BillController::class, 'store'])->name('bills.store');
        Route::post('/bills/{bill}/pay', [BillController::class, 'payBill'])->name('bills.pay');
        Route::post('/bills/{bill}/cancel-bills', [BillController::class, 'cancelBills'])->name('bills.cancelBills');
        Route::post('/bills/{bill}/refund', [RefundController::class, 'store'])->name('bills.refund');
        Route::get('/bills/{bill}/export', [BillController::class, 'export'])->name('bills.export');

        // Ghi nhận thanh toán thủ công
        Route::post('/payments/{bill}', [PaymentController::class, 'store'])->name('payments.store');

        // Thông báo hệ thống (admin gửi)
        Route::resource('/notifications', NotificationController::class)->except(['create', 'show', 'edit', 'update']);
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Login, Register, Forgot Password...)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
