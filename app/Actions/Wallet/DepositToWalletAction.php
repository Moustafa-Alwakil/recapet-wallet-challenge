<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\ExceptionCode;
use App\Exceptions\DepositToWalletFailedException;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class DepositToWalletAction
{
    public Wallet $wallet;

    public function __invoke(Wallet $wallet, int $amount): void
    {
        DB::beginTransaction();

        try {
            /** @var Wallet $wallet */
            $wallet = $wallet->lockForUpdate()->find($wallet->id);

            $this->wallet = $wallet;

            $wallet->balance += $amount;

            $wallet->save();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw DepositToWalletFailedException::new(
                exceptionCode: ExceptionCode::DEPOSIT_TO_WALLET_FAILED,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Deposit failed due to system error.',
                description: $exception->getMessage(),
            );
        }
    }
}
