<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Wallet\DepositToMyWalletController;
use App\Http\Controllers\Api\V1\Wallet\MyWalletBalanceController;
use App\Http\Controllers\Api\V1\Wallet\MyWalletLedgerEntriesController;
use App\Http\Controllers\Api\V1\Wallet\TransferFromMyWalletController;
use App\Http\Controllers\Api\V1\Wallet\WithdrawFromMyWalletController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'throttle:60,1',
    'auth:sanctum',
])->name('wallet.')
    ->group(function () {
        Route::prefix('my-wallet')
            ->name('my-wallet.')
            ->group(function () {
                Route::middleware('ensure_active_wallet')
                    ->group(function () {
                        Route::get('balance', MyWalletBalanceController::class)->name('balance');
                        Route::get('ledger-entries', MyWalletLedgerEntriesController::class)->name('ledger-entries');

                        Route::middleware([
                            //                            'ensure_idempotency',
                        ])
                            ->group(function () {
                                Route::post('deposit', DepositToMyWalletController::class)->name('deposit');
                                Route::post('withdraw', WithdrawFromMyWalletController::class)->name('withdraw');
                                Route::post('transfer', TransferFromMyWalletController::class)->name('transfer');
                            });
                    });
            });
    });
