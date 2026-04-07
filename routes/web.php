<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Midtrans webhook (no auth, but verified via signature)
Route::post('/midtrans/callback', [\App\Http\Controllers\Admin\TransactionController::class, 'midtransCallback'])->name('midtrans.callback');

Route::middleware('auth')->group(function () {
    // Dashboard: accessible by admin & owner
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->middleware('role:admin,owner')
        ->name('dashboard');

    // Profile (admin + employee + owner)
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ── ADMIN-ONLY routes (CRUD management) ─────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Customer Management
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);

        // Employee Management
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);

        // Inventory & Catalog
        Route::resource('stock', \App\Http\Controllers\Admin\StockController::class);
        Route::post('stock/{stock}/adjust', [\App\Http\Controllers\Admin\StockController::class, 'adjust'])->name('stock.adjust');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        Route::post('services/{service}/toggle', [\App\Http\Controllers\Admin\ServiceController::class, 'toggle'])->name('services.toggle');
        Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class);
        Route::post('discounts/{discount}/toggle', [\App\Http\Controllers\Admin\DiscountController::class, 'toggle'])->name('discounts.toggle');
        Route::get('discounts-generate-code', [\App\Http\Controllers\Admin\DiscountController::class, 'generateCode'])->name('discounts.generate-code');

        // Transactions (CRUD - admin only: create, store, destroy, update-status, update-info)
        Route::post('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/create', [\App\Http\Controllers\Admin\TransactionController::class, 'create'])->name('transactions.create');
        Route::delete('transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::post('transactions/{transaction}/update-status', [\App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::put('transactions/{transaction}/update-info', [\App\Http\Controllers\Admin\TransactionController::class, 'updateInfo'])->name('transactions.update-info');
        Route::get('transactions-search-customers', [\App\Http\Controllers\Admin\TransactionController::class, 'searchCustomers'])->name('transactions.search-customers');
        Route::get('transactions-check-discount', [\App\Http\Controllers\Admin\TransactionController::class, 'checkDiscount'])->name('transactions.check-discount');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });

    // ── ADMIN + OWNER routes (monitoring / read) ─────────────
    Route::middleware('role:admin,owner')->prefix('admin')->name('admin.')->group(function () {
        // Transactions (read-only + payment actions)
        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::get('transactions/{transaction}/invoice', [\App\Http\Controllers\Admin\TransactionController::class, 'invoice'])->name('transactions.invoice');
        Route::get('transactions/{transaction}/check-payment', [\App\Http\Controllers\Admin\TransactionController::class, 'checkPaymentStatus'])->name('transactions.check-payment');
        Route::get('transactions/{transaction}/snap-token', [\App\Http\Controllers\Admin\TransactionController::class, 'getSnapToken'])->name('transactions.snap-token');
        Route::post('transactions/{transaction}/approve-cancel', [\App\Http\Controllers\Admin\TransactionController::class, 'approveCancel'])->name('transactions.approve-cancel');
        Route::get('transactions-export', [\App\Http\Controllers\Admin\TransactionController::class, 'export'])->name('transactions.export');

        // Laporan Keuangan
        Route::get('laporan-keuangan', [\App\Http\Controllers\Admin\LaporanKeuanganController::class, 'index'])->name('laporan-keuangan.index');
        Route::get('laporan-keuangan/print', [\App\Http\Controllers\Admin\LaporanKeuanganController::class, 'print'])->name('laporan-keuangan.print');
        Route::get('laporan-keuangan/export', [\App\Http\Controllers\Admin\LaporanKeuanganController::class, 'export'])->name('laporan-keuangan.export');

        // Attendance monitoring
        Route::get('attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/report', [\App\Http\Controllers\Admin\AttendanceController::class, 'report'])->name('attendances.report');
        Route::get('attendances/clock', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockPage'])->name('attendances.clock');
        Route::post('attendances/clock-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
        Route::post('attendances/clock-out', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockOut'])->name('attendances.clock-out');
    });

    // Employee routes
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('transactions', \App\Http\Controllers\Employee\TransactionController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('transactions-search-customers', [\App\Http\Controllers\Employee\TransactionController::class, 'searchCustomers'])->name('transactions.search-customers');
        Route::get('transactions-check-discount', [\App\Http\Controllers\Employee\TransactionController::class, 'checkDiscount'])->name('transactions.check-discount');
        Route::get('attendances/clock', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockPage'])->name('attendances.clock');
        Route::post('attendances/clock-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockIn'])->name('attendances.clock-in');
        Route::post('attendances/clock-out', [\App\Http\Controllers\Admin\AttendanceController::class, 'clockOut'])->name('attendances.clock-out');
    });
});


Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Customer\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [\App\Http\Controllers\Customer\AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\Customer\AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [\App\Http\Controllers\Customer\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Customer\AuthController::class, 'register'])->name('register.post');

    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/transactions', [\App\Http\Controllers\Customer\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [\App\Http\Controllers\Customer\TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/check-payment', [\App\Http\Controllers\Customer\TransactionController::class, 'checkPaymentStatus'])->name('transactions.check-payment');
        Route::get('/transactions/{transaction}/snap-token', [\App\Http\Controllers\Customer\TransactionController::class, 'getSnapToken'])->name('transactions.snap-token');
        Route::post('/transactions/{transaction}/request-cancel', [\App\Http\Controllers\Customer\TransactionController::class, 'requestCancel'])->name('transactions.request-cancel');

        Route::get('/orders/create', [\App\Http\Controllers\Customer\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'store'])->name('orders.store');
    });
});
