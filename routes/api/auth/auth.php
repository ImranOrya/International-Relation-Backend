<?php

use App\Http\Controllers\api\auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key'])->group(function () {
    Route::post('/auth-login', [AuthController::class, 'login']);

    Route::post('/ngo-auth-login', [AuthController::class, 'loginNgo']);

    Route::get('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
    Route::post('/auth-logout', [AuthController::class, 'logout']);
    Route::get('/auth-user', [AuthController::class, 'user']);
    // Super user will use this route for user update
    Route::post('/auth/user/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/user/change-permissions', [AuthController::class, 'changePermissions']);
    Route::delete('/auth/user/delete/{id}', [AuthController::class, "delete"]);
});
