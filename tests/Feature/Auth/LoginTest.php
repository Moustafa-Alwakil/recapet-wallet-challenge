<?php

declare(strict_types=1);

use App\Models\User;

it('logins user successfully', function (): void {
    User::factory()->create([
        'email' => 'moustafaalwakil@gmail.com',
        'password' => 'Password@123',
    ]);

    $response = $this->postJson(
        uri: route('api.v1.auth.login'),
        data: [
            'email' => 'moustafaalwakil@gmail.com',
            'password' => 'Password@123',
            'device_name' => 'Moustafa',
        ],
    );

    $response->assertSuccessful()
        ->assertJsonPath('data.user.email', 'moustafaalwakil@gmail.com')
        ->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
});

it('validates correctly', function (array $data, string $errorField): void {
    User::factory()->create([
        'email' => 'moustafaalwakil@gmail.com',
        'password' => 'Password@123',
    ]);

    $response = $this->postJson(
        uri: route('api.v1.auth.login'),
        data: [$data],
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors($errorField);
})->with([
    // user existence
    [
        [
            'email' => 'not_exists@gmail.com',
            'password' => 'Password@123',
            'device_name' => 'Moustafa',
        ],
        'email',
    ],
    // right password
    [
        [
            'email' => 'moustafaalwakil@gmail.com',
            'password' => 'wrong_password',
            'device_name' => 'Moustafa',
        ],
        'email',
    ],
]);
