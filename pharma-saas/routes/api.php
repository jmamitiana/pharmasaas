<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SaleApiController;
use App\Http\Controllers\Api\StockApiController;
use App\Http\Controllers\Api\TransferApiController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/search', [ProductApiController::class, 'search']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    
    Route::get('/stock', [StockApiController::class, 'index']);
    Route::get('/stock/refresh', [StockApiController::class, 'refresh']);
    Route::get('/stock/{product}', [StockApiController::class, 'show']);
    
    Route::get('/sales', [SaleApiController::class, 'index']);
    Route::post('/sales', [SaleApiController::class, 'store']);
    Route::get('/sales/{sale}', [SaleApiController::class, 'show']);
    
    Route::get('/transfers', [TransferApiController::class, 'index']);
    Route::post('/transfers', [TransferApiController::class, 'store']);
    Route::get('/transfers/{transfer}', [TransferApiController::class, 'show']);
    Route::post('/transfers/{transfer}/receive', [TransferApiController::class, 'receive']);
});

Route::post('/webhook/mvola', function () {
    $data = request()->all();
    $paymentService = new \App\Services\PaymentService('mvola');
    $paymentService->handleWebhook('mvola', $data);
    return response()->json(['status' => 'ok']);
});

Route::post('/webhook/orange-money', function () {
    $data = request()->all();
    $paymentService = new \App\Services\PaymentService('orange_money');
    $paymentService->handleWebhook('orange_money', $data);
    return response()->json(['status' => 'ok']);
});
