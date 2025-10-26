<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\ExceptionCode;
use App\Enums\WalletDepositStatus;
use App\Exceptions\DepositToWalletFailedException;
use App\Models\Wallet;
use App\Models\WalletDeposit;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DepositToWalletAction
{
    public WalletDeposit $deposit;

    public Wallet $wallet;

    /**
     * @throws DepositToWalletFailedException|Throwable
     */
    public function __invoke(Wallet $wallet, int $amountInCents): void
    {
        $this->deposit = $wallet->deposits()
            ->create([
                'amount_in_cents' => $amountInCents,
            ]);

        DB::beginTransaction();

        try {
            /** @var Wallet $wallet */
            $wallet = $wallet->lockForUpdate()->find($wallet->id);
            $this->wallet = $wallet;

            $this->addToWalletBalance();
            $this->markDepositAsSucceeded();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->markDepositAsFailed();

            throw DepositToWalletFailedException::new(
                exceptionCode: ExceptionCode::DEPOSIT_TO_WALLET_FAILED,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Deposit failed due to system error.',
                description: $exception->getMessage(),
            );
        }
    }

    private function addToWalletBalance(): void
    {
        $this->wallet->balance_in_cents += $this->deposit->amount_in_cents;

        $this->wallet->save();
    }

    private function markDepositAsFailed(): void
    {
        $this->deposit->update([
            'status' => WalletDepositStatus::FAILED,
        ]);
    }

    private function markDepositAsSucceeded(): void
    {
        $this->deposit->update([
            'status' => WalletDepositStatus::SUCCEEDED,
        ]);
    }
}
