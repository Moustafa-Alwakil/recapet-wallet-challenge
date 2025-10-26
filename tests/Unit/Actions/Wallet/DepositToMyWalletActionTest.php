<?php

declare(strict_types=1);

use App\Actions\Wallet\DepositToWalletAction;
use App\Enums\WalletDepositStatus;
use App\Models\User;

it('succeeds and updates balances and status', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $action = app(DepositToWalletAction::class);

    $action(
        wallet: $user->wallet,
        amountInCents: 3000,
    );

    $user->refresh();

    expect($action->deposit->status)
        ->toBe(WalletDepositStatus::SUCCEEDED)
        ->and($user->wallet->balance_in_cents)
        ->toBe(10000 + 3000);
});
