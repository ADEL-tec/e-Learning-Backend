<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PayController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::any('/courseList', [CourseController::class, 'courseList']);
    Route::any('/courseDetail', [CourseController::class, 'courseDetail']);
    Route::any('/checkout', [PayController::class, 'checkout']);
});

// Route::post('/auth/register', [UserController::class, 'createUser']);
// Route::post('/auth/login', [UserController::class, 'loginUser']);
