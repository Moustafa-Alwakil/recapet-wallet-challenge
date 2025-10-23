<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\DataTransferObjects\User\UserDTO;
use App\Models\User;

final class CreateUserAction
{
    public User $user;

    public function __invoke(UserDTO $userDTO): void
    {
        $this->user = new User;

        $this->user->name = $userDTO->name;
        $this->user->email = $userDTO->email;
        $this->user->password = $userDTO->password;

        $this->user->save();
    }
}
