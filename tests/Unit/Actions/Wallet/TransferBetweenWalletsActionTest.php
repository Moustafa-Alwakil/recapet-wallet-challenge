<?php

declare(strict_types=1);

use App\Actions\Wallet\TransferBetweenWalletsAction;
use App\Enums\WalletTransferStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;

it('succeeds and updates balances and status', function (): void {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $sender->wallet->update(['balance_in_cents' => 10000]);

    $action = app(TransferBetweenWalletsAction::class);

    $action(
        senderWallet: $sender->wallet,
        receiverWallet: $receiver->wallet,
        amountInCents: 3000,
    );

    $sender->refresh();
    $receiver->refresh();

    expect($action->walletTransfer->status)
        ->toBe(WalletTransferStatus::SUCCEEDED)
        ->and($sender->wallet->balance_in_cents)
        ->toBe(10000 - 3550)
        ->and($receiver->wallet->balance_in_cents)
        ->toBe(3000);
});

it('throws insufficient balance and marks transfer as failed without changing balances', function (): void {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $sender->wallet->update(['balance_in_cents' => 1000]);

    $action = app(TransferBetweenWalletsAction::class);

    $initialSenderBalance = $sender->wallet->balance_in_cents;
    $initialReceiverBalance = $receiver->wallet->balance_in_cents;

    expect(
        fn () => $action(
            senderWallet: $sender->wallet,
            receiverWallet: $receiver->wallet,
            amountInCents: 2000,
        )
    )
        ->toThrow(InsufficientBalanceException::class)
        ->and($sender->wallet->out_transfers->last())
        ->not
        ->toBeNull()
        ->and($sender->wallet->out_transfers->last()->status)
        ->toBe(WalletTransferStatus::FAILED);

    $sender->refresh();
    $receiver->refresh();

    expect($sender->wallet->balance_in_cents)
        ->toBe($initialSenderBalance)
        ->and($receiver->wallet->balance_in_cents)
        ->toBe($initialReceiverBalance);
});
