<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

final readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
