<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wallet;

use App\Actions\Wallet\DepositToWalletAction;
use App\Actions\Wallet\WithdrawFromWalletAction;
use App\Exceptions\DepositToWalletFailedException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WithdrawFromWalletFailedException;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\Wallet\DepositWalletRequest;
use App\Http\Requests\Api\Wallet\WithdrawWalletRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @property-read User $authUser
 */
final class WalletController extends ApiBaseController
{
    public function balance(Request $request): JsonResponse
    {
        return CustomJsonResponse::success(
            data: [
                'wallet' => $this->authUser->wallet->toResource(),
            ]
        );
    }

    /**
     * @throws DepositToWalletFailedException
     */
    public function deposit(DepositWalletRequest $request, DepositToWalletAction $depositWalletAction): JsonResponse
    {
        $depositWalletAction($this->authUser->wallet, $request->integer('amount'));

        return CustomJsonResponse::success(
            message: "🎉 Deposit successful! {$depositWalletAction->wallet->textual_balance} added to your wallet.",
            data: [
                'wallet' => $this->authUser->wallet->toResource(),
            ]
        );
    }

    /**
     * @throws WithdrawFromWalletFailedException|InsufficientBalanceException
     */
    public function withdraw(WithdrawWalletRequest $request, WithdrawFromWalletAction $withdrawWalletAction): JsonResponse
    {
        $withdrawWalletAction($this->authUser->wallet, $request->integer('amount'));

        return CustomJsonResponse::success(
            message: "🥳 Withdrawal successful! {$withdrawWalletAction->withdrawalRequest->textual_amount} has been deducted from your wallet.",
            data: [
                'wallet' => $withdrawWalletAction->wallet->toResource(),
            ]
        );
    }
}
