<?php

use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/cancel', [HomeController::class, 'cancel']);
Route::get('/success', [HomeController::class, 'success']);
Route::get('/checkout-pay', [HomeController::class, 'checkoutPay']);
