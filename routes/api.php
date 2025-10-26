<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::name('api.')
    ->group(function () {
        Route::prefix('v1')
            ->name('v1.')
            ->group(base_path('routes/api/v1/routes.php'));
    });
