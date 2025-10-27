<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

arch()->preset()->php();
arch()->preset()->security();

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->not
    ->toUse([
        'dd',
        'ddd',
        'dump',
        'env',
        'exit',
        'ray',
    ]);

expect('App\Observers')
    ->toBeClasses();

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class)
    ->toOnlyBeUsedIn([
        'App\Actions',
        'App\Providers',
        'App\Models',
        'App\Http\Requests',
        'App\Http\Resources',
        'App\Observers',
        'App\Console\Commands',
    ])
    ->ignoring(User::class);

arch()
    ->expect('App\Http\Controllers')
    ->toOnlyBeUsedIn('routes')
    ->ignoring(ApiBaseController::class);

arch()
    ->expect(ApiBaseController::class)
    ->toOnlyBeUsedIn('App\Http\Controllers\Api\V1');

expect('App\Http\Requests')
    ->classes()
    ->toHaveSuffix('Request')
    ->and('App\Http\Requests')
    ->toExtend(FormRequest::class)
    ->and('App\Http\Requests')
    ->toHaveMethod('rules');

arch()
    ->expect('App\Traits')
    ->toBeTraits();

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch()
    ->expect('App\Enums')
    ->toBeEnums();

expect('App\Http\Controllers')
    ->not
    ->toHavePublicMethodsBesides([
        '__construct',
        '__invoke',
        'index',
        'show',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
        'middleware',
    ])
    ->and('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');
