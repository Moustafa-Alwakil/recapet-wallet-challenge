<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Contracts\FeeCalculatorContract;
use App\Enums\ExceptionCode;
use App\Enums\WalletTransferStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransferBetweenWalletsFailedException;
use App\Models\Wallet;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class TransferBetweenWalletsAction
{
    public WalletTransfer $walletTransfer;

    public Wallet $senderWallet;

    public Wallet $receiverWallet;

    public function __invoke(Wallet $senderWallet, Wallet $receiverWallet, int $amountInCents): void
    {
        $this->walletTransfer = $senderWallet->out_transfers()
            ->create([
                'amount_in_cents' => $amountInCents,
                'fee_in_cents' => app(FeeCalculatorContract::class)->calculate($amountInCents),
                'receiver_wallet_id' => $receiverWallet->id,
            ]);

        DB::beginTransaction();

        try {
            /** @var Wallet $senderWallet */
            $senderWallet = $senderWallet->lockForUpdate()->find($senderWallet->id);
            $this->senderWallet = $senderWallet;

            /** @var Wallet $receiverWallet */
            $receiverWallet = $receiverWallet->lockForUpdate()->find($receiverWallet->id);
            $this->receiverWallet = $receiverWallet;

            throw_unless(
                condition: $senderWallet->hasSufficientBalance($this->walletTransfer->amount_in_cents + $this->walletTransfer->fee_in_cents),
                exception: InsufficientBalanceException::new(
                    exceptionCode: ExceptionCode::TRANSFER_FAILED_DUE_TO_INSUFFICIENT_BALANCE,
                    statusCode: Response::HTTP_BAD_REQUEST,
                    message: 'Insufficient balance to complete this transaction, please add funds to your wallet to continue.',
                )
            );

            $this->deductFromSenderWalletBalance();
            $this->addToReceiverWalletBalance();
            $this->markTransferAsSucceeded();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->markTransferAsFailed();

            throw_if(
                condition: $exception instanceof InsufficientBalanceException,
                exception: $exception
            );

            throw TransferBetweenWalletsFailedException::new(
                exceptionCode: ExceptionCode::TRANSFER_BETWEEN_WALLETS_FAILED,
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: 'Transfer failed due to system error.',
                description: $exception->getMessage(),
            );
        }
    }

    private function deductFromSenderWalletBalance(): void
    {
        $this->senderWallet->balance_in_cents -= $this->walletTransfer->total_in_cents;

        $this->senderWallet->save();
    }

    private function addToReceiverWalletBalance(): void
    {
        $this->receiverWallet->balance_in_cents += $this->walletTransfer->amount_in_cents;

        $this->receiverWallet->save();
    }

    private function markTransferAsFailed(): void
    {
        $this->walletTransfer->update([
            'status' => WalletTransferStatus::FAILED,
        ]);
    }

    private function markTransferAsSucceeded(): void
    {
        $this->walletTransfer->update([
            'status' => WalletTransferStatus::SUCCEEDED,
        ]);
    }
}
