<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginController extends ApiBaseController
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->str('email')->toString())
            ->first();

        if (! $user || ! Hash::check($request->str('password')->toString(), $user->password)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        $accessToken = $user->createToken(
            name: $request->str('device_name')->toString(),
            expiresAt: now()->addDay()
        );

        return CustomJsonResponse::success(
            message: 'Logged in successfully.',
            data: [
                'user' => $user->toResource(),
                'token' => $accessToken->plainTextToken,
            ],
        );
    }
}
