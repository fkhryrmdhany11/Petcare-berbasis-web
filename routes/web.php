<?php

use App\Http\Controllers\Admin\AdminAdoptionController;
use App\Http\Controllers\Admin\AdminAnimalController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminHealthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shop & Cart Routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/cart', [ShopController::class, 'cart'])->name('cart.index');
Route::post('/cart/add', [ShopController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [ShopController::class, 'updateCart'])->name('cart.update');

// Adoption & Health Services Public Catalog
Route::get('/adoption', [AdoptionController::class, 'index'])->name('adoption.index');
Route::get('/health-services', [HealthServiceController::class, 'index'])->name('health.index');

// Authenticated Customer Routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [ShopController::class, 'checkoutForm'])->name('checkout.index');
    Route::post('/checkout', [ShopController::class, 'processCheckout'])->name('checkout.store');
    Route::get('/orders/history', [ShopController::class, 'orderHistory'])->name('orders.history');
    Route::get('/orders/{order}/invoice', [ShopController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/cancel', [ShopController::class, 'cancelOrder'])->name('orders.cancel');

    Route::post('/adoption', [AdoptionController::class, 'store'])->name('adoption.store');
    Route::get('/adoption/history', [AdoptionController::class, 'history'])->name('adoption.history');
    Route::post('/adoption/{adoption}/cancel', [AdoptionController::class, 'cancel'])->name('adoption.cancel');

    Route::post('/health-services/book', [HealthServiceController::class, 'storeBooking'])->name('health.book');
    Route::get('/health-services/bookings/{booking}/card', [HealthServiceController::class, 'bookingCard'])->name('health.card');
    Route::post('/health-services/bookings/{booking}/cancel', [HealthServiceController::class, 'cancelBooking'])->name('health.cancel');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/pets', [ProfileController::class, 'storePet'])->name('profile.pets.store');
    Route::delete('/profile/pets/{pet}', [ProfileController::class, 'destroyPet'])->name('profile.pets.destroy');
});

// Unified Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/quick-action', [AdminDashboardController::class, 'quickAction'])->name('quick-action');

    // Products CRUD
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::post('/products/{product}/update', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/delete', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Animals CRUD
    Route::get('/animals', [AdminAnimalController::class, 'index'])->name('animals.index');
    Route::post('/animals', [AdminAnimalController::class, 'store'])->name('animals.store');
    Route::post('/animals/{animal}/update', [AdminAnimalController::class, 'update'])->name('animals.update');
    Route::post('/animals/{animal}/delete', [AdminAnimalController::class, 'destroy'])->name('animals.destroy');

    // Adoptions Management
    Route::get('/adoptions', [AdminAdoptionController::class, 'index'])->name('adoptions.index');
    Route::get('/adoptions/{adoption}', [AdminAdoptionController::class, 'show'])->name('adoptions.show');
    Route::post('/adoptions/{adoption}/status', [AdminAdoptionController::class, 'updateStatus'])->name('adoptions.status');

    // Health Services & Bookings Management
    Route::get('/services', [AdminHealthController::class, 'services'])->name('services.index');
    Route::post('/services', [AdminHealthController::class, 'storeService'])->name('services.store');
    Route::post('/services/{service}/update', [AdminHealthController::class, 'updateService'])->name('services.update');
    Route::post('/services/{service}/delete', [AdminHealthController::class, 'destroyService'])->name('services.destroy');

    Route::get('/bookings', [AdminHealthController::class, 'bookings'])->name('bookings.index');
    Route::post('/bookings/{booking}/status', [AdminHealthController::class, 'updateBookingStatus'])->name('bookings.status');
    Route::post('/bookings/{booking}/delete', [AdminHealthController::class, 'destroyBooking'])->name('bookings.destroy');
});
