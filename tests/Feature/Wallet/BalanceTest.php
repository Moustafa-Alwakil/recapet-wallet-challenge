<?php

declare(strict_types=1);

use App\Models\User;

it('shows balance correctly', function (): void {
    $user = User::factory()->create();

    $user->wallet->update(['balance_in_cents' => 10000]);

    $this->actingAs($user, 'sanctum');

    $response = $this->getJson(uri: route('api.v1.wallet.my-wallet.balance'));

    $response->assertSuccessful()
        ->assertJsonPath('data.wallet.balance_in_cents', 10000)
        ->assertJsonPath('data.wallet.balance', 100)
        ->assertJsonPath('data.wallet.textual_balance', '$100.00');
});
