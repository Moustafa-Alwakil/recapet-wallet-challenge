<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\User\CreateUserAction;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends ApiBaseController
{
    public function __invoke(RegisterRequest $request, CreateUserAction $createUserAction): JsonResponse
    {
        $user = $createUserAction($request->toDTO());

        return CustomJsonResponse::success(
            message: 'Registered successfully.',
            data: [
                new UserResource($user),
            ],
            statusCode: Response::HTTP_CREATED
        );
    }
}
