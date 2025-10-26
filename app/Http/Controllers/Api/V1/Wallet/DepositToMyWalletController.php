<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Actions\Wallet\DepositToWalletAction;
use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Http\Requests\Api\V1\Wallet\DepositToMyWalletRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;

/**
 * @property-read User $authUser
 */
final class DepositToMyWalletController extends ApiBaseController
{
    public function __invoke(DepositToMyWalletRequest $request, DepositToWalletAction $depositToWalletAction): JsonResponse
    {
        try {
            $depositToWalletAction($this->authUser->wallet, $request->amountInCents);

            return CustomJsonResponse::success(
                message: "🎉 Deposit successful! {$depositToWalletAction->deposit->textual_amount} added to your wallet.",
                data: [
                    'deposit' => $depositToWalletAction->deposit->toResource(),
                    'wallet' => $depositToWalletAction->wallet->toResource(),
                ]
            );
        } finally {
            $request->releaseWalletLock();
        }
    }
}
