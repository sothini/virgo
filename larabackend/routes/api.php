<?php

use App\Events\OrderMatched;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/user_orders', [OrderController::class, 'userOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/symbols', [OrderController::class, 'getSymbols']);
});

Route::get('/debug-job', function () {
    // Put breakpoint here in your IDE
    event(new OrderMatched(
        Order::first(),
        Order::first(),
        '1',
        1,
        1,
        1,
        1
    ));
    // \App\Jobs\MatchAllJob::dispatchSync();
    return 'Job dispatched';
});