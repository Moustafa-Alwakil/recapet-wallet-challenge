<?php

declare(strict_types=1);

use App\Models\User;

it('registers user successfully', function (): void {
    $response = $this->postJson(
        uri: route('api.v1.auth.register'),
        data: [
            'name' => 'Moustafa Alwakil',
            'email' => 'moustafaalwakil@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ],
    );

    $response->assertCreated()
        ->assertJsonPath('data.user.name', 'Moustafa Alwakil')
        ->assertJsonPath('data.user.email', 'moustafaalwakil@gmail.com');

    expect(User::count())
        ->toBe(1)
        ->and(User::first()->toArray())
        ->toMatchArray([
            'name' => 'Moustafa Alwakil',
            'email' => 'moustafaalwakil@gmail.com',
        ]);
});

it('validates correctly', function (array $data, string $errorField): void {
    $user = User::factory()->create([
        'email' => 'moustafaalwakil@gmail.com',
    ]);

    $response = $this->postJson(
        uri: route('api.v1.auth.register'),
        data: [$data],
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors($errorField);
})->with([
    // email uniqueness
    [
        [
            'name' => 'Moustafa Alwakil',
            'email' => 'moustafaalwakil@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ],
        'email',
    ],
    // password confirmation
    [
        [
            'name' => 'Moustafa Alwakil',
            'email' => 'moustafaalwakil@gmail.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ],
        'email',
    ],
]);
