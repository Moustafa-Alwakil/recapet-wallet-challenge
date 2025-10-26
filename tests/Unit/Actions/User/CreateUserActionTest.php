<?php

declare(strict_types=1);

use App\Actions\User\CreateUserAction;
use App\DataTransferObjects\User\UserDTO;
use App\Models\User;

it('creates user successfully', function (): void {
    $userDto = new UserDTO(
        name: 'Moustafa Alwakil',
        email: 'moustafaalwakil@gmail.com',
        password: 'Password@123',
    );

    $action = app(CreateUserAction::class);

    $action(
        userDTO: $userDto,
    );

    $this->assertDatabaseCount(User::class, 1);
    $this->assertDatabaseHas(User::class, [
        'name' => $userDto->name,
        'email' => $userDto->email,
    ]);
});
