<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LoginRequest extends FormRequest
{
    /**
     * @return array<string|array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', Rule::email()->strict()],
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ];
    }
}
