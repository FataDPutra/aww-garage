<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\OfferPriceController;

// Halaman utama
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard hanya untuk user yang sudah login
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Middleware untuk membedakan Admin dan Customer
Route::middleware(['auth'])->group(function () {
    
    // ✅ Routes untuk **Customer** (Hanya Customer yang bisa mengakses)
    Route::middleware(['role:customer'])->group(function () {

        Route::get('/purchase-requests', [PurchaseRequestController::class, 'index'])->name('purchase_requests.index');
        Route::get('/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
        Route::post('/purchase-requests', [PurchaseRequestController::class, 'store'])->name('purchase_requests.store');
        Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show'])->name('purchase_requests.show');

        Route::post('/purchase-requests/{id}/accept-offer', [PurchaseRequestController::class, 'acceptOffer'])->name('purchase_requests.acceptOffer');
        Route::post('/purchase-requests/{id}/reject-offer', [PurchaseRequestController::class, 'rejectOffer'])->name('purchase_requests.rejectOffer');

        Route::get('/payments/{offerprice_id}/payment-dp', [PaymentController::class, 'createDP'])->name('payments.payment-dp');
        Route::post('/payments/{offerprice_id}/payment-dp', [PaymentController::class, 'storeDP'])->name('payments.store-dp');
        Route::get('/payments/{offerprice_id}/payment-full', [PaymentController::class, 'createFull'])->name('payments.payment-full');
        Route::post('/payments/{offerprice_id}/payment-full', [PaymentController::class, 'storeFull'])->name('payments.store-full');

        Route::get('/orders', [OrderController::class, 'indexCustomer'])->name('orders-customer.index');
        Route::get('/orders/{order_id}', [OrderController::class, 'showCustomer'])->name('orders-customer.show');
        Route::post('/orders/{order_id}/confirm-shipment-customer', [OrderController::class, 'confirmShipmentCustomer']);
        Route::post('/orders/{order_id}/confirm', [OrderController::class, 'confirmCustomerOrder'])->name('orders.confirm');

    });

    // ✅ Routes untuk **Admin** (Hanya Admin yang bisa mengakses)
    Route::middleware(['role:admin'])->group(function () {
        // Admin - Purchase Request
        Route::get('/admin/purchase-requests', [PurchaseRequestController::class, 'adminIndex'])->name('purchase_requests.admin_index');
        Route::post('/offer-prices/{pr_id}', [OfferPriceController::class, 'store'])->name('offer_prices.store');

        // Admin - Orders
        // Halaman daftar pesanan
        Route::get('admin/orders', [OrderController::class, 'index'])->name('orders.index');

        // Halaman detail pesanan
        Route::get('admin/orders/{order_id}', [OrderController::class, 'show'])->name('orders.show');

        // Konfirmasi penerimaan barang oleh admin
        Route::post('admin/orders/{order_id}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');

        // Upload hasil pengerjaan
        Route::post('admin/orders/{order_id}/upload-completed', [OrderController::class, 'uploadCompletedPhoto'])->name('orders.upload-completed');

        // Tandai pesanan siap dikirim
        Route::post('admin/orders/{order_id}/ready-to-ship', [OrderController::class, 'markAsReadyToShip'])->name('orders.ready-to-ship');

        Route::post('/admin/orders/{order_id}/upload-revision-photo', [OrderController::class, 'uploadRevisionPhoto']);

        // Admin - Shipping
        Route::post('/admin/shippings/{order_id}', [ShippingController::class, 'store'])->name('shippings.store');
        Route::post('/admin/shippings/{shipping_id}/mark-delivered', [ShippingController::class, 'markAsDelivered'])->name('shippings.markAsDelivered');
    
        // Admin - Services
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');


        Route::get('/purchaserequests', [PurchaseRequestController::class, 'adminIndex'])->name('admin.purchaserequests.index');
        Route::get('/purchaserequests/show/{id}', [PurchaseRequestController::class, 'showAdmin'])->name('admin.purchaserequests.show');
        Route::post('/purchaserequests/{id}/offer', [PurchaseRequestController::class, 'storeOfferPrice'])->name('admin.purchaserequests.offer');
    });
});


// Include file autentikasi Laravel Breeze
require __DIR__.'/auth.php';
