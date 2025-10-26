<?php

declare(strict_types=1);

use App\Actions\Wallet\WithdrawFromWalletAction;
use App\Enums\ExceptionCode;
use App\Enums\WalletLedgerEntryType;
use App\Enums\WalletTransferStatus;
use App\Enums\WalletWithdrawalRequestStatus;
use App\Exceptions\WithdrawFromWalletFailedException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('withdraw successfully and updates balances, status and ledger entries', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $this->actingAs($user, 'sanctum');

    $amount = '30.00';

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.withdraw'),
        data: [
            'amount' => $amount,
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.withdrawal_request.status', WalletTransferStatus::SUCCEEDED->current())
        ->assertJsonPath('data.withdrawal_request.amount_in_cents', 3000)
        ->assertJsonPath('data.withdrawal_request.amount', 30)
        ->assertJsonPath('data.withdrawal_request.textual_amount', '$30.00');

    $user->refresh();

    expect($user->wallet->balance_in_cents)
        ->toBe(10000 - 3000)
        ->and($user->wallet->ledger_entries)
        ->toHaveCount(1)
        ->and($user->wallet->ledger_entries->first()->type)
        ->toBe(WalletLedgerEntryType::DEBIT);
});

it('validates amount constraints', function (string $amount, string $errorField): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
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

it('fails with bad request when balance is insufficient and does not change balances', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 1000]);

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.withdraw'),
        data: [
            'amount' => '20.00',
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertBadRequest();

    expect($user->wallet->withdrawal_requests->last())
        ->not
        ->toBeNull()
        ->and($user->wallet->withdrawal_requests->last()->status)
        ->toBe(WalletWithdrawalRequestStatus::FAILED);

    $user->refresh();

    expect($user->wallet->balance_in_cents)
        ->toBe(1000);
});

it('returns 500 and leaves balances unchanged when a system error occurs during transfer', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $this->mock(WithdrawFromWalletAction::class)
        ->shouldReceive('__invoke')
        ->andThrow(WithdrawFromWalletFailedException::new(
            exceptionCode: ExceptionCode::WITHDRAW_FROM_WALLET_FAILED,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            message: 'Withdrawal failed due to system error.',
        ));

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.withdraw'),
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
