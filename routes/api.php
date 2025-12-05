<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;


Route::get('/test', function () {
    return response()->json(['message' => 'API Working!']);
});


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

// public product routes
Route::get('/products', [ProductController::class,'index']);
Route::get('/products/{id}', [ProductController::class,'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class,'logout']);

    // product management (admin only enforced in controller via Gate)
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // orders
    Route::get('/orders', [OrderController::class,'index']);
    Route::post('/orders', [OrderController::class,'store']);
    Route::get('/orders/{id}', [OrderController::class,'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class,'cancel']);
});
