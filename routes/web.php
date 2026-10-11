<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranController;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Welcome Landing Page (Mobile-First)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Direct Forgot Password & Reset Flow
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Midtrans Webhook (CSRF Excluded)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');

// Distance Validation API Endpoint for Realtime Leaflet.js Map Check
Route::post('/api/distance/validate', function (Request $request, DistanceService $distanceService) {
    $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);

    $lat = (float) $request->input('latitude');
    $lng = (float) $request->input('longitude');

    $result = $distanceService->validateDistance($lat, $lng);
    $outlet = $distanceService->getOutletCoordinates();

    return response()->json([
        'is_valid' => $result['is_valid'],
        'distance_km' => $result['distance_km'],
        'max_radius_km' => $result['max_radius_km'],
        'message' => $result['message'],
        'outlet' => $outlet,
    ]);
})->name('api.distance.validate');

// Pelanggan Routes (Web Mobile: max-w-md mx-auto)
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/dashboard', [PelangganController::class, 'dashboard'])->name('dashboard');
    Route::get('/pesanan/buat', [PelangganController::class, 'createOrder'])->name('orders.create');
    Route::post('/pesanan', [PelangganController::class, 'storeOrder'])->name('orders.store');
    Route::get('/pesanan', [PelangganController::class, 'orders'])->name('orders');
    Route::get('/pesanan/{id}', [PelangganController::class, 'showOrder'])->name('orders.show');
    Route::get('/pesanan/{id}/detail', [PelangganController::class, 'showOrder'])->name('pesanan.detail');
    Route::get('/pesanan/{id}/bayar', [PembayaranController::class, 'showPayment'])->name('orders.pay');
    Route::get('/pesanan/{id}/pembayaran', [PembayaranController::class, 'showPayment'])->name('pesanan.pay');
    Route::post('/pesanan/{order}/simulate-pay', [PembayaranController::class, 'simulatePayment'])->name('pesanan.simulate_pay');
    Route::post('/pesanan/{id}/simulasi-bayar', [PembayaranController::class, 'simulatePayment'])->name('orders.simulate_pay');
    Route::get('/profil', [PelangganController::class, 'profile'])->name('profile');
    Route::get('/notifikasi', [PelangganController::class, 'notifications'])->name('notifications');
});

// Driver Routes (Web Mobile: max-w-md mx-auto)
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::get('/pesanan', [DriverController::class, 'orders'])->name('orders');
    Route::get('/pesanan/{id}', [DriverController::class, 'showOrder'])->name('orders.show');
    Route::post('/tugas/{id}/update', [DriverController::class, 'updateTaskStatus'])->name('tasks.update');
    Route::post('/ketersediaan', [DriverController::class, 'updateAvailability'])->name('availability');
    Route::post('/lokasi', [DriverController::class, 'updateLocation'])->name('location.update');
    Route::get('/profil', [DriverController::class, 'profile'])->name('profile');
});

// Admin Routes (Web Responsive: Sidebar Left Nav)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orderan', [AdminController::class, 'orders'])->name('orderan');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/assign-driver', [AdminController::class, 'assignDriver'])->name('orders.assign_driver');
    Route::post('/orders/{id}/issue-invoice', [AdminController::class, 'issueInvoice'])->name('orders.issue_invoice');
    Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('orders.update_status');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
    Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/export-excel', [AdminReportController::class, 'exportExcel'])->name('laporan.export_excel');
    Route::get('/laporan/export-pdf', [AdminReportController::class, 'exportPdf'])->name('laporan.export_pdf');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/produk', [AdminController::class, 'products'])->name('produk');
    Route::post('/layanan', [AdminController::class, 'storeLayanan'])->name('layanan.store');
    Route::put('/layanan/{id}', [AdminController::class, 'updateLayanan'])->name('layanan.update');
    Route::delete('/layanan/{id}', [AdminController::class, 'deleteLayanan'])->name('layanan.delete');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::delete('/kategori/{id}', [AdminController::class, 'deleteKategori'])->name('kategori.delete');
    Route::get('/drivers', [AdminController::class, 'drivers'])->name('drivers');
    Route::get('/driver', [AdminController::class, 'drivers'])->name('driver');
    Route::post('/drivers', [AdminController::class, 'storeDriver'])->name('drivers.store');
});
