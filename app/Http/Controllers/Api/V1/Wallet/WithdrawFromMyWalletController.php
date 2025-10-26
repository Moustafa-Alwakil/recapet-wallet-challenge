<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Actions\Wallet\WithdrawFromWalletAction;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WithdrawFromWalletFailedException;
use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Http\Requests\Api\V1\Wallet\WithdrawFromMyWalletRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;

/**
 * @property-read User $authUser
 */
final class WithdrawFromMyWalletController extends ApiBaseController
{
    /**
     * @throws WithdrawFromWalletFailedException|InsufficientBalanceException
     */
    public function __invoke(WithdrawFromMyWalletRequest $request, WithdrawFromWalletAction $withdrawFromWalletAction): JsonResponse
    {
        $withdrawFromWalletAction($this->authUser->wallet, $request->amountInCents);

        return CustomJsonResponse::success(
            message: "🥳 Withdrawal successful! {$withdrawFromWalletAction->withdrawalRequest->textual_amount} has been deducted from your wallet.",
            data: [
                'withdrawal_request' => $withdrawFromWalletAction->withdrawalRequest->toResource(),
                'wallet' => $withdrawFromWalletAction->wallet->toResource(),
            ]
        );
    }
}
