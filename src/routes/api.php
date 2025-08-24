<?php

use App\Http\Controllers\{
    AuthController,
    CategoryController,
    ProductController,
    CartController,
    OrderController,
    StatsController
};
use Illuminate\Support\Facades\Route;

// --- AUTH ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- PUBLIC ---
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products',   [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// --- PROTECTED (auth:api) ---
Route::middleware('auth:api')->group(function () {

    // Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // --- ADMIN (auth:api + admin) prefix'li ---
    Route::prefix('admin')->middleware('admin')->group(function () {
        // İstatistik
        Route::get('/stats', [StatsController::class, 'index']);

        // Sipariş durum güncelle
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // Kategoriler (admin)
        Route::post('/categories',              [CategoryController::class, 'store']);
        Route::put('/categories/{category}',    [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Ürünler (admin)
        Route::post('/products',               [ProductController::class, 'store']);
        Route::put('/products/{product}',      [ProductController::class, 'update']);
        Route::delete('/products/{product}',   [ProductController::class, 'destroy']);
    });

    // --- EK: Admin CRUD (prefix YOK) -> Postman uyumluluğu ---
    Route::middleware('admin')->group(function () {
        // Kategoriler
        Route::post('/categories',              [CategoryController::class, 'store']);
        Route::put('/categories/{category}',    [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Ürünler
        Route::post('/products',               [ProductController::class, 'store']);
        Route::put('/products/{product}',      [ProductController::class, 'update']);
        Route::delete('/products/{product}',   [ProductController::class, 'destroy']);
    });

    // Cart
    Route::get('/cart',                    [CartController::class, 'getCart']);
    Route::post('/cart/add',               [CartController::class, 'add']);
    Route::put('/cart/update',             [CartController::class, 'updateQty']);
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove']);
    Route::delete('/cart/clear',           [CartController::class, 'clear']);

    // Orders
    Route::middleware('throttle:orders')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });
    Route::get('/orders',          [OrderController::class, 'index']);
    Route::get('/orders/{order}',  [OrderController::class, 'show']);
});

// 404
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint bulunamadı',
        'data'    => null,
        'errors'  => [],
    ], 404);
});
