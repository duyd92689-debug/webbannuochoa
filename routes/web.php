<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// ============================================================
// TRANG CHỦ & CỬA HÀNG - Giữ nguyên từ Lab 01 & 02
// ============================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('perfumes', PerfumeController::class);
Route::resource('categories', CategoryController::class);

use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\GHNWebhookController;

// ============================================================
// 🚚 THIRD-PARTY WEBHOOKS & CALLBACKS (GHN, MOMO IPN)
// NOTE: 
// - Không dùng middleware 'auth' vì bên thứ 3 (GHN, MoMo) gọi sang tự động.
// - Đã được bypass CSRF trong bootstrap/app.php.
// ============================================================
Route::post('/ghn/webhook', [GHNWebhookController::class, 'handle'])->name('ghn.webhook');
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

// ============================================================
// GIỎ HÀNG & ĐẶT HÀNG - Yêu cầu đăng nhập (auth middleware)
// ============================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/{perfume}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/gio-hang/{itemKey}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/gio-hang/{itemKey}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/dat-hang', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('/gio-hang-nguoi-dung', [CartController::class, 'index'])->name('user.cart.index');

    // Payment & GHN Shipping (Aliased routes)
    Route::get('/payment', [UserOrderController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [UserOrderController::class, 'processPayment'])->name('payment.process');

    // User Orders History & Tracking (Aliased routes)
    Route::get('/orders', [UserOrderController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
});

// Nhóm route chuẩn theo tài liệu PDF (prefix 'user', name 'user.')
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Payment
    Route::get('/payment', [UserOrderController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [UserOrderController::class, 'processPayment'])->name('payment.process');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders/{order}/momo-qr', [MomoController::class, 'showQr'])->name('orders.momo.qr');
    Route::get('/orders', [UserOrderController::class, 'orderHistory'])->name('orders.index');
    // Trang hướng dẫn thanh toán ATM (nội địa / quốc tế)
    Route::get('/orders/{order}/payment-pending', [UserOrderController::class, 'paymentPending'])->name('orders.payment.pending');
    // Xác nhận thanh toán thành công (ATM / Visa / MoMo)
    Route::post('/orders/{order}/confirm-payment', [UserOrderController::class, 'confirmPayment'])->name('orders.confirm.payment');

    // Lab 7: Livechat User (PDF Trang 7-8)
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
});

// Tra cứu địa giới hành chính & cước vận chuyển GHN
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
});

// ============================================================
// TRA CỨU & KIỂM TRA ĐƠN HÀNG (Public)
// ============================================================
Route::get('/kiem-tra-don-hang', [UserOrderController::class, 'trackingForm'])->name('orders.tracking');
Route::post('/kiem-tra-don-hang', [UserOrderController::class, 'trackingSearch'])->name('orders.tracking.search');
Route::get('/tra-cuu-don-hang', [UserOrderController::class, 'trackingForm']);

// ============================================================
// KHÁCH HÀNG - Đăng ký & Đăng nhập cửa hàng (Lab 03)
// ============================================================
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// XÁC THỰC EMAIL (Lab 03)
// ============================================================
// Hiển thị thông báo xác thực email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Xử lý link xác nhận (từ email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome'); // Redirect về trang chủ
})->middleware(['auth', 'signed'])->name('verification.verify');

// Gửi lại email xác nhận
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ============================================================
// ADMIN - Trang đăng nhập & quản trị RIÊNG BIỆT
// Truy cập qua: http://localhost:8000/admin/login
// ============================================================

// Đăng nhập / Đăng xuất Admin
Route::get('/admin', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang quản trị viên!');
    }
    return redirect()->route('admin.login');
});
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// Khu vực quản trị (yêu cầu đăng nhập với quyền admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('/products', ProductController::class, ['as' => 'admin']);
    Route::resource('/categories', CategoryController::class, ['as' => 'admin']);
    Route::post('/orders/bulk-update', [OrderController::class, 'bulkUpdate'])->name('admin.orders.bulk_update');
    Route::resource('/orders', OrderController::class, ['as' => 'admin'])->only(['index', 'show', 'update']);

    // Lab 8: Reports (Báo cáo doanh thu & biểu đồ - PDF Trang 7)
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/charts', [AdminReportController::class, 'charts'])->name('admin.reports.charts');

    // Lab 8: Quản lý người dùng (PDF Trang 21)
    Route::resource('/users', AdminUserController::class, ['as' => 'admin']);

    // Lab 7: Livechat Admin (PDF Trang 8)
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('admin.chat.users');
    Route::get('/chat/search-customers', [AdminChatController::class, 'searchCustomers'])->name('admin.chat.search_customers');
    Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('admin.chat.send');
});

// Route phụ trợ tương thích Lab 03
Route::get('/welcome', [HomeController::class, 'index'])->name('welcome');
Route::get('/danh-muc', [CategoryController::class, 'index'])->name('user.categories.index');
Route::get('/lich-su-don-hang', [UserOrderController::class, 'orderHistory'])->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/products/{product}', [ProductController::class, 'show_normal'])->name('products.show');
});


