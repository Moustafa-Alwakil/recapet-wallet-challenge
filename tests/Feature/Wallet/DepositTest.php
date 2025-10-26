<?php

declare(strict_types=1);

use App\Actions\Wallet\DepositToWalletAction;
use App\Enums\ExceptionCode;
use App\Enums\WalletLedgerEntryType;
use App\Enums\WalletTransferStatus;
use App\Exceptions\DepositToWalletFailedException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('deposits successfully and updates balances, status and ledger entries', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');

    $amount = '100.50';

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.deposit'),
        data: [
            'amount' => $amount,
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.deposit.status', WalletTransferStatus::SUCCEEDED->current())
        ->assertJsonPath('data.deposit.amount_in_cents', 10050)
        ->assertJsonPath('data.deposit.amount', 100.5)
        ->assertJsonPath('data.deposit.textual_amount', '$100.50');

    $user->refresh();

    expect($user->wallet->balance_in_cents)
        ->toBe(10050)
        ->and($user->wallet->ledger_entries)
        ->toHaveCount(1)
        ->and($user->wallet->ledger_entries->first()->type)
        ->toBe(WalletLedgerEntryType::CREDIT);
});

it('validates amount constraints', function (string $amount, string $errorField): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.deposit'),
        data: [
            'amount' => $amount,
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrorFor($errorField);
})->with([
    // below min
    ['0.50', 'amount'],
    // more than 2 decimal places
    ['10.123', 'amount'],
    // above max
    ['100001', 'amount'],
]);

it('returns 500 and leaves balances unchanged when a system error occurs during transfer', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $this->mock(DepositToWalletAction::class)
        ->shouldReceive('__invoke')
        ->andThrow(DepositToWalletFailedException::new(
            exceptionCode: ExceptionCode::DEPOSIT_TO_WALLET_FAILED,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            message: 'Deposit failed due to system error.',
        ));

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.deposit'),
        data: [
            'amount' => '25.00',
        ],
        headers: $this->addIdempotencyKey()
    );

    $response->assertInternalServerError();

    $user->refresh();

    expect($user->wallet->balance_in_cents)
        ->toBe(10000);
});
