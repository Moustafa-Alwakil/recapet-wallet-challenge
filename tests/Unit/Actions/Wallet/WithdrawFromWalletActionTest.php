<?php

declare(strict_types=1);

use App\Actions\Wallet\WithdrawFromWalletAction;
use App\Enums\WalletWithdrawalRequestStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;

it('succeeds and updates balances and status', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $action = app(WithdrawFromWalletAction::class);

    $action(
        wallet: $user->wallet,
        amountInCents: 3000,
    );

    $user->refresh();

    expect($action->withdrawalRequest->status)
        ->toBe(WalletWithdrawalRequestStatus::SUCCEEDED)
        ->and($user->wallet->balance_in_cents)
        ->toBe(10000 - 3000);
});

it('throws insufficient balance and marks transfer as failed without changing balances', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 1000]);

    $action = app(WithdrawFromWalletAction::class);

    expect(
        fn () => $action(
            wallet: $user->wallet,
            amountInCents: 2000,
        )
    )
        ->toThrow(InsufficientBalanceException::class)
        ->and($user->wallet->withdrawal_requests->last())
        ->not
        ->toBeNull()
        ->and($user->wallet->withdrawal_requests->last()->status)
        ->toBe(WalletWithdrawalRequestStatus::FAILED);

    $user->refresh();

    expect($user->wallet->balance_in_cents)
        ->toBe(1000);
});
