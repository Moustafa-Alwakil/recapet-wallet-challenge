<?php

declare(strict_types=1);

use App\Actions\Wallet\TransferBetweenWalletsAction;
use App\Enums\ExceptionCode;
use App\Enums\WalletLedgerEntryType;
use App\Enums\WalletTransferStatus;
use App\Exceptions\TransferBetweenWalletsFailedException;
use App\Models\User;
use App\Models\WalletLedgerEntry;
use Symfony\Component\HttpFoundation\Response;

it('transfers successfully and updates balances, status and ledger entries', function (): void {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $sender->wallet->update(['balance_in_cents' => 10000]);

    $this->actingAs($sender, 'sanctum');

    $amount = '30.00';

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
        data: [
            'receiver_user_id' => $receiver->id,
            'amount' => $amount,
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.transfer.status', WalletTransferStatus::SUCCEEDED->current())
        ->assertJsonPath('data.transfer.amount_in_cents', 3000)
        ->assertJsonPath('data.transfer.fee_in_cents', 550)
        ->assertJsonPath('data.transfer.total_in_cents', 3550)
        ->assertJsonPath('data.transfer.amount_in_cents', 3000)
        ->assertJsonPath('data.transfer.fee', 5.50)
        ->assertJsonPath('data.transfer.total', 35.5)
        ->assertJsonPath('data.transfer.amount', 30)
        ->assertJsonPath('data.transfer.textual_fee', '$5.50')
        ->assertJsonPath('data.transfer.textual_total', '$35.50')
        ->assertJsonPath('data.transfer.textual_amount', '$30.00');

    $sender->refresh();
    $receiver->refresh();

    expect($sender->wallet->balance_in_cents)
        ->toBe(10000 - 3550)
        ->and($receiver->wallet->balance_in_cents)
        ->toBe(3000)
        ->and(WalletLedgerEntry::query()->get())
        ->toHaveCount(3)
        ->and($sender->wallet->ledger_entries)
        ->toHaveCount(2)
        ->and($sender->wallet->ledger_entries->pluck('type')->all())
        ->toContain(WalletLedgerEntryType::DEBIT)
        ->toContain(WalletLedgerEntryType::FEE)
        ->and($receiver->wallet->ledger_entries)
        ->toHaveCount(1)
        ->and($receiver->wallet->ledger_entries->first()->type)
        ->toBe(WalletLedgerEntryType::CREDIT);
});

it('prevents self-transfer via validation', function (): void {
    $sender = User::factory()->create();

    $this->actingAs($sender, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
        data: [
            'receiver_user_id' => $sender->id,
            'amount' => '10.00',
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrorFor('receiver_user_id');
});

it('validates amount constraints', function (string $amount, string $errorField): void {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $this->actingAs($sender, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
        data: [
            'receiver_user_id' => $receiver->id,
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
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $sender->wallet->update(['balance_in_cents' => 1000]);

    $this->actingAs($sender, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
        data: [
            'receiver_user_id' => $receiver->id,
            'amount' => '20.00',
        ],
        headers: $this->addIdempotencyKey(),
    );

    $response->assertBadRequest();

    expect($sender->wallet->out_transfers->last())
        ->not
        ->toBeNull()
        ->and($sender->wallet->out_transfers->last()->status)
        ->toBe(WalletTransferStatus::FAILED);

    $sender->refresh();
    $receiver->refresh();

    expect($sender->wallet->balance_in_cents)
        ->toBe(1000)
        ->and($receiver->wallet->balance_in_cents)
        ->toBe(0);
});

it('returns 500 and leaves balances unchanged when a system error occurs during transfer', function (): void {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $sender->wallet->update(['balance_in_cents' => 10000]);

    $this->mock(TransferBetweenWalletsAction::class)
        ->shouldReceive('__invoke')
        ->andThrow(TransferBetweenWalletsFailedException::new(
            exceptionCode: ExceptionCode::TRANSFER_BETWEEN_WALLETS_FAILED,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            message: 'Transfer failed due to system error.',
        ));

    $this->actingAs($sender, 'sanctum');

    $response = $this->postJson(
        uri: route('api.v1.wallet.my-wallet.transfer'),
        data: [
            'receiver_user_id' => $receiver->id,
            'amount' => '25.00',
        ],
        headers: $this->addIdempotencyKey()
    );

    $response->assertInternalServerError();

    $sender->refresh();
    $receiver->refresh();

    expect($sender->wallet->balance_in_cents)
        ->toBe(10000)
        ->and($receiver->wallet->balance_in_cents)
        ->toBe(0);
});
