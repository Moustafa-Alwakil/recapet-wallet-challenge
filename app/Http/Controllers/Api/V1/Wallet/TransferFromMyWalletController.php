<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Actions\Wallet\TransferBetweenWalletsAction;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransferBetweenWalletsFailedException;
use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Http\Requests\Api\V1\Wallet\TransferFromMyWalletRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;

/**
 * @property-read User $authUser
 */
final class TransferFromMyWalletController extends ApiBaseController
{
    /**
     * @throws TransferBetweenWalletsFailedException|InsufficientBalanceException
     */
    public function __invoke(TransferFromMyWalletRequest $request, TransferBetweenWalletsAction $transferBetweenWalletsAction): JsonResponse
    {
        try {
            $transferBetweenWalletsAction($this->authUser->wallet, $request->receiverUserWallet, $request->amountInCents);

            return CustomJsonResponse::success(
                message: "🥳 Transfer successful! {$transferBetweenWalletsAction->walletTransfer->textual_amount} has been transferred from your wallet.",
                data: [
                    'transfer' => $transferBetweenWalletsAction->walletTransfer->toResource(),
                    'wallet' => $transferBetweenWalletsAction->senderWallet->toResource(),
                ]
            );
        } finally {
            $request->releaseWalletLock();
        }
    }
}
