<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\MoveoutController;

// 1. ส่วน Login/Logout
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset (OTP via Email)
Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendOtp'])->name('password.email');
Route::get('/password/otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp');
Route::post('/password/otp/verify', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/password/otp/resend', [PasswordResetController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('/password/reset/form', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// 2. ส่วนของ Admin (ต้อง Login + เป็น Admin หรือ Staff)
Route::middleware(['auth', 'role:admin,staff'])->group(function () {

    // หน้าหลัก Dashboard
    Route::get('/dashboard', [AuthController::class, 'index'])->name('admin.dashboard');

    // หน้าข้อมูลการเข้าพัก
    Route::get('/accommodation', [AuthController::class, 'accommodation'])->name('rooms.accommodation');

    // หน้าจัดการมิเตอร์ (รวม - เก็บไว้ backwards compat)
    Route::get('/meters', [MeterController::class, 'index'])->name('rooms.meters');
    Route::post('/meters/update', [MeterController::class, 'update'])->name('meters.update');

    // หน้ามิเตอร์น้ำ
    Route::get('/meters/water', [MeterController::class, 'waterIndex'])->name('meters.water');
    Route::post('/meters/water/update', [MeterController::class, 'updateWater'])->name('meters.water.update');

    // หน้ามิเตอร์ไฟฟ้า
    Route::get('/meters/electric', [MeterController::class, 'electricIndex'])->name('meters.electric');
    Route::post('/meters/electric/update', [MeterController::class, 'updateElectric'])->name('meters.electric.update');

    // หน้าประวัติบิล
    Route::get('/bills', [BillController::class, 'index'])->name('rooms.bills');

    // หน้าสร้างบิล
    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::get('/bills/create/{id}', [BillController::class, 'createForRoom'])->name('bills.createForRoom');
    Route::get('/bills/room-data', [BillController::class, 'getRoomData'])->name('bills.roomData');
    Route::post('/bills/store', [BillController::class, 'store'])->name('bills.store');
    Route::post('/bills/store-all', [BillController::class, 'storeAll'])->name('bills.storeAll');
    Route::get('/bills/view/{id}', [BillController::class, 'view'])->name('bills.view');

    // ยืนยันการชำระและใบเสร็จ
    Route::post('/bills/{id}/confirm-payment', [BillController::class, 'confirmPayment'])->name('bills.confirmPayment');
    Route::get('/bills/{id}/receipt', [BillController::class, 'viewReceipt'])->name('bills.receipt');

    // หน้าข้อมูลลูกค้า
    Route::get('/customers', [CustomerController::class, 'index'])->name('rooms.customers');
    Route::post('/customers/update', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'editForm'])->name('customers.editForm');
    Route::put('/customers/{id}', [CustomerController::class, 'updateFull'])->name('customers.updateFull');
    Route::delete('/contracts/{id}', [CustomerController::class, 'destroyContract'])->name('contracts.destroy');

    // แจ้งซ่อม (admin จัดการคำร้องจาก tenant)
    Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/{id}', [RepairController::class, 'show'])->name('repairs.show');
    Route::post('/repairs/{id}/status', [RepairController::class, 'updateStatus'])->name('repairs.updateStatus');

    // แจ้งย้ายออก (admin จัดการคำร้องจาก tenant)
    Route::get('/moveout-requests', [MoveoutController::class, 'index'])->name('moveout.requests');
    Route::get('/moveout-requests/{id}', [MoveoutController::class, 'show'])->name('moveout.show');
    Route::post('/moveout-requests/{id}/approve', [MoveoutController::class, 'approve'])->name('moveout.approve');
    Route::post('/moveout-requests/{id}/reject', [MoveoutController::class, 'reject'])->name('moveout.reject');

    // เก็บไว้ (backwards compat)
    Route::get('/rooms/{id}/moveout', [RoomController::class, 'moveOutForm'])->name('rooms.moveout');
    Route::post('/contracts/moveout', [CustomerController::class, 'moveOut'])->name('contracts.moveout');


    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{id}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('rooms.update'); // เตรียมไว้สำหรับปุ่มบันทึก


    // หน้าสร้างสัญญาเช่า (หน้าใหม่)
    Route::get('/rooms/{id}/contract/create', [RoomController::class, 'createContract'])->name('rooms.createContract');

    // บันทึกสัญญาเช่า
    Route::post('/rooms/{id}/contract/store', [RoomController::class, 'storeContract'])->name('rooms.storeContract');

    // ดูสัญญาเช่า (admin)
    Route::get('/rooms/{id}/contract/view', [RoomController::class, 'viewContract'])->name('rooms.viewContract');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.index');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Tenant Account Creation
    Route::get('/tenants/create', [CustomerController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [CustomerController::class, 'store'])->name('tenants.store');

});

// 3. ส่วนของ Tenant (ต้อง Login + เป็น Tenant)
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->group(function () {
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');
    Route::get('/bills', [TenantDashboardController::class, 'bills'])->name('tenant.bills');
    Route::get('/bills/{id}', [TenantDashboardController::class, 'viewBill'])->name('tenant.bills.view');
    Route::post('/bills/{id}/upload-slip', [TenantDashboardController::class, 'uploadSlip'])->name('tenant.bills.uploadSlip');
    Route::get('/bills/{id}/receipt', [TenantDashboardController::class, 'receipt'])->name('tenant.receipt');
    Route::get('/contract', [TenantDashboardController::class, 'contract'])->name('tenant.contract');
    Route::get('/contract/detail', [TenantDashboardController::class, 'contractDetail'])->name('tenant.contract.detail');
    Route::get('/contract/history', [TenantDashboardController::class, 'contractHistory'])->name('tenant.contract.history');
    Route::get('/meters', [TenantDashboardController::class, 'meters'])->name('tenant.meters');
    Route::get('/room-info', [TenantDashboardController::class, 'roomInfo'])->name('tenant.room-info');
    Route::get('/moveout', [TenantDashboardController::class, 'moveout'])->name('tenant.moveout');
    Route::post('/moveout', [TenantDashboardController::class, 'storeMoveout'])->name('tenant.moveout.store');
    Route::get('/moveout/status', [TenantDashboardController::class, 'moveoutStatus'])->name('tenant.moveout.status');
    Route::get('/profile', [TenantDashboardController::class, 'profile'])->name('tenant.profile');
    Route::put('/profile', [TenantDashboardController::class, 'updateProfile'])->name('tenant.profile.update');

    // Repairs
    Route::get('/repairs', [TenantDashboardController::class, 'repairIndex'])->name('tenant.repairs');
    Route::get('/repairs/create', [TenantDashboardController::class, 'repairCreate'])->name('tenant.repairs.create');
    Route::post('/repairs', [TenantDashboardController::class, 'repairStore'])->name('tenant.repairs.store');

    // Notifications
    Route::get('/notifications/{id}/read', [TenantDashboardController::class, 'readNotification'])->name('tenant.notifications.read');
    Route::post('/notifications/mark-all-read', [TenantDashboardController::class, 'markAllRead'])->name('tenant.notifications.markAllRead');
});
