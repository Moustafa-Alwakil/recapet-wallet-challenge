<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')
    ->group(function () {
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::post('logout', LogoutController::class);
            });
    });
