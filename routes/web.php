<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PelangganController;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
    Route::get('/pesanan/{id}/bayar', [PelangganController::class, 'payOrder'])->name('orders.pay');
    Route::post('/pesanan/{id}/simulasi-bayar', [PelangganController::class, 'simulatePaymentSuccess'])->name('orders.simulate_pay');
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
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/assign-driver', [AdminController::class, 'assignDriver'])->name('orders.assign_driver');
    Route::post('/orders/{id}/issue-invoice', [AdminController::class, 'issueInvoice'])->name('orders.issue_invoice');
    Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('orders.update_status');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/layanan', [AdminController::class, 'storeLayanan'])->name('layanan.store');
    Route::put('/layanan/{id}', [AdminController::class, 'updateLayanan'])->name('layanan.update');
    Route::delete('/layanan/{id}', [AdminController::class, 'deleteLayanan'])->name('layanan.delete');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::delete('/kategori/{id}', [AdminController::class, 'deleteKategori'])->name('kategori.delete');
    Route::get('/drivers', [AdminController::class, 'drivers'])->name('drivers');
    Route::post('/drivers', [AdminController::class, 'storeDriver'])->name('drivers.store');
});
