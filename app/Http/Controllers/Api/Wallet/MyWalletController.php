<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wallet;

use App\Actions\Wallet\DepositToWalletAction;
use App\Actions\Wallet\TransferBetweenWalletsAction;
use App\Actions\Wallet\WithdrawFromWalletAction;
use App\Exceptions\DepositToWalletFailedException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WithdrawFromWalletFailedException;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\Wallet\DepositToMyWalletRequest;
use App\Http\Requests\Api\Wallet\TransferFromMyWalletRequest;
use App\Http\Requests\Api\Wallet\WithdrawFromMyWalletRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @property-read User $authUser
 */
final class MyWalletController extends ApiBaseController
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
    public function deposit(DepositToMyWalletRequest $request, DepositToWalletAction $depositToWalletAction): JsonResponse
    {
        $depositToWalletAction($this->authUser->wallet, $request->amountInCents);

        return CustomJsonResponse::success(
            message: "🎉 Deposit successful! {$depositToWalletAction->deposit->textual_amount} added to your wallet.",
            data: [
                'deposit' => $depositToWalletAction->deposit->toResource(),
                'wallet' => $depositToWalletAction->wallet->toResource(),
            ]
        );
    }

    /**
     * @throws WithdrawFromWalletFailedException|InsufficientBalanceException
     */
    public function withdraw(WithdrawFromMyWalletRequest $request, WithdrawFromWalletAction $withdrawFromWalletAction): JsonResponse
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

    /**
     * @throws WithdrawFromWalletFailedException|InsufficientBalanceException
     */
    public function transfer(TransferFromMyWalletRequest $request, TransferBetweenWalletsAction $transferBetweenWalletsAction): JsonResponse
    {
        $transferBetweenWalletsAction($this->authUser->wallet, $request->receiverUserWallet, $request->amountInCents);

        return CustomJsonResponse::success(
            message: "🥳 Transfer successful! {$transferBetweenWalletsAction->walletTransfer->textual_amount} has been transferred from your wallet.",
            data: [
                'transfer' => $transferBetweenWalletsAction->walletTransfer->toResource(),
                'wallet' => $transferBetweenWalletsAction->senderWallet->toResource(),
            ]
        );
    }
}
