<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Wallet\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'throttle:60,1',
    'auth:sanctum',
])->group(function () {
    Route::prefix('wallet')
        ->controller(WalletController::class)
        ->group(function () {
            Route::get('balance', 'balance');
            Route::post('deposit', 'deposit');
            Route::post('withdraw', 'withdraw');
        });
});
