<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\DataTransferObjects\User\UserDTO;
use App\Models\User;

final class CreateUserAction
{
    public function __invoke(UserDTO $userDTO): User
    {
        $user = new User;

        $user->name = $userDTO->name;
        $user->email = $userDTO->email;
        $user->password = $userDTO->password;

        $user->save();

        return $user;
    }
}
