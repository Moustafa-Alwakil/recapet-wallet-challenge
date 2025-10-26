<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @property-read User $authUser
 */
final class MyWalletBalanceController extends ApiBaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        return CustomJsonResponse::success(
            data: [
                'wallet' => $this->authUser->wallet->toResource(),
            ]
        );
    }
}
