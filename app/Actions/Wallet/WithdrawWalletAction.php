<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\ExceptionCode;
use App\Enums\WalletWithdrawalRequestStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WithdrawWalletFailedException;
use App\Models\Wallet;
use App\Models\WalletWithdrawalRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class WithdrawWalletAction
{
    public WalletWithdrawalRequest $withdrawalRequest;

    public Wallet $wallet;

    public function __invoke(Wallet $wallet, int $amount): void
    {
        $this->withdrawalRequest = WalletWithdrawalRequest::query()
            ->create([
                'amount' => $amount,
                'wallet_id' => $wallet->id,
            ]);

        DB::beginTransaction();

        /** @var Wallet $wallet */
        $wallet = $wallet->lockForUpdate()->find($wallet->id);

        $this->wallet = $wallet;

        try {
            if ($wallet->balance < $this->withdrawalRequest->amount) {
                throw InsufficientBalanceException::new(
                    exceptionCode: ExceptionCode::INSUFFICIENT_BALANCE,
                    statusCode: Response::HTTP_BAD_REQUEST,
                    message: 'Insufficient balance to complete this transaction, please add funds to your wallet to continue.',
                );
            }

            $this->deductFromWalletBalance();
            $this->markWithdrawalRequestAsSucceeded();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            $this->markWithdrawalRequestAsFailed();

            if ($exception instanceof InsufficientBalanceException) {
                throw $exception;
            }

            throw WithdrawWalletFailedException::new(
                exceptionCode: ExceptionCode::WITHDRAW_WALLET_FAILED,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Withdraw failed due to system error.',
                description: $exception->getMessage(),
            );
        }
    }

    private function deductFromWalletBalance(): void
    {
        $this->wallet->balance -= $this->withdrawalRequest->amount;

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
