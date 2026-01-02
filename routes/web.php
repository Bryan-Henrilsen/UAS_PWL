<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// ROUTE UNTUK TAMU (BELUM LOGIN)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.view');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// ROUTE YANG BUTUH LOGIN
Route::middleware('auth')->group(function () {
    // Manajemen User (Hanya Super Admin)
    Route::group(['middleware' => ['role:Super Admin']], function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);

        Route::prefix('reports')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
            Route::get('/stock', [\App\Http\Controllers\ReportController::class, 'stock'])->name('reports.stock');
            Route::get('/inbound', [\App\Http\Controllers\ReportController::class, 'inbound'])->name('reports.inbound');
            Route::get('/outbound', [\App\Http\Controllers\ReportController::class, 'outbound'])->name('reports.outbound');
        });
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Product
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    // Route Inbound
    Route::resource('inbounds', \App\Http\Controllers\InboundController::class);
    // Route Inbound untuk approval & cancel
    Route::post('/inbounds/{id}/approve', [\App\Http\Controllers\InboundController::class, 'approve'])->name('inbounds.approve');
    Route::post('/inbounds/{id}/cancel', [\App\Http\Controllers\InboundController::class, 'cancel'])->name('inbounds.cancel');
    Route::post('/inbounds/{id}/reject', [\App\Http\Controllers\InboundController::class, 'reject'])->name('inbounds.reject');
    Route::post('/inbounds/{id}/update-note', [\App\Http\Controllers\InboundController::class, 'updateNote'])->name('inbounds.update_note');

    // Route Outbound
    Route::resource('outbounds', \App\Http\Controllers\OutboundController::class);
    // Route Outbound untuk approval dan cancel
    Route::post('/outbounds/{id}/approve', [\App\Http\Controllers\OutboundController::class, 'approve'])->name('outbounds.approve');
    Route::post('/outbounds/{id}/cancel', [\App\Http\Controllers\OutboundController::class, 'cancel'])->name('outbounds.cancel');
    Route::post('/outbounds/{id}/reject', [\App\Http\Controllers\OutboundController::class, 'reject'])->name('outbounds.reject');
    Route::post('/outbounds/{id}/update-note', [\App\Http\Controllers\OutboundController::class, 'updateNote'])->name('outbounds.update_note');

    // Route Stock Opname
    Route::resource('stock_opnames', \App\Http\Controllers\StockOpnameController::class);
    Route::post('/stock_opnames/{id}/approve', [\App\Http\Controllers\StockOpnameController::class, 'approve'])->name('stock_opnames.approve');
    Route::post('/stock_opnames/{id}/cancel', [\App\Http\Controllers\StockOpnameController::class, 'cancel'])->name('stock_opnames.cancel');
});