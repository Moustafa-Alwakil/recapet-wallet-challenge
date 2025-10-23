<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\DataTransferObjects\User\UserDTO;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class RegisterRequest extends FormRequest
{
    /**
     * @return array<string|array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', Rule::email()->strict(), Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function toDTO(): UserDTO
    {
        return new UserDTO(
            name: $this->str('name')->toString(),
            email: $this->str('email')->toString(),
            password: $this->str('password')->toString(),
        );
    }
}
