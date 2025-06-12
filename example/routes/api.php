<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 訂單相關API
Route::get('/orders/stats', [OrderController::class, 'stats']);
Route::resource('/orders', OrderController::class);

// 商品相關API
Route::resource('/products', ProductController::class);
