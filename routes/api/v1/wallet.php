<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Wallet\MyWalletController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'throttle:60,1',
    'auth:sanctum',
])->group(function () {
    Route::prefix('my-wallet')
        ->controller(MyWalletController::class)
        ->group(function () {
            Route::get('balance', 'balance');

            Route::middleware('active_wallet')
                ->group(function () {
                    Route::post('deposit', 'deposit');
                    Route::post('withdraw', 'withdraw');
                    Route::post('transfer', 'transfer');
                });
        });
});
