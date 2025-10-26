<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\ExceptionCode;
use App\Enums\WalletWithdrawalRequestStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WithdrawFromWalletFailedException;
use App\Models\Wallet;
use App\Models\WalletWithdrawalRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class WithdrawFromWalletAction
{
    public WalletWithdrawalRequest $withdrawalRequest;

    public Wallet $wallet;

    /**
     * @throws InsufficientBalanceException|WithdrawFromWalletFailedException|Throwable
     */
    public function __invoke(Wallet $wallet, int $amountInCents): void
    {
        $this->withdrawalRequest = WalletWithdrawalRequest::query()
            ->create([
                'amount_in_cents' => $amountInCents,
                'wallet_id' => $wallet->id,
            ]);

        DB::beginTransaction();

        try {
            /** @var Wallet $wallet */
            $wallet = $wallet->lockForUpdate()->find($wallet->id);
            $this->wallet = $wallet;

            throw_unless(
                condition: $wallet->hasSufficientBalance($amountInCents),
                exception: InsufficientBalanceException::new(
                    exceptionCode: ExceptionCode::WITHDRAWAL_FAILED_DUE_TO_INSUFFICIENT_BALANCE,
                    statusCode: Response::HTTP_BAD_REQUEST,
                    message: 'Insufficient balance to complete this transaction, please add funds to your wallet to continue.',
                )
            );

            $this->deductFromWalletBalance();
            $this->markWithdrawalRequestAsSucceeded();

            DB::commit();
        } catch (InsufficientBalanceException $insufficientBalanceException) {
            DB::rollBack();

            $this->markWithdrawalRequestAsFailed();

            throw $insufficientBalanceException;
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->markWithdrawalRequestAsFailed();

            throw WithdrawFromWalletFailedException::new(
                exceptionCode: ExceptionCode::WITHDRAW_FROM_WALLET_FAILED,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Withdraw failed due to system error.',
                description: $exception->getMessage(),
            );
        }
    }

    private function deductFromWalletBalance(): void
    {
        $this->wallet->balance_in_cents -= $this->withdrawalRequest->amount_in_cents;

        $this->wallet->save();
    }

    private function markWithdrawalRequestAsFailed(): void
    {
        $this->withdrawalRequest->update([
            'status' => WalletWithdrawalRequestStatus::FAILED,
        ]);
    }

    private function markWithdrawalRequestAsSucceeded(): void
    {
        $this->withdrawalRequest->update([
            'status' => WalletWithdrawalRequestStatus::SUCCEEDED,
        ]);
    }
}
