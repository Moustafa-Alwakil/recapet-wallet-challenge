<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Models\User;
use App\Responses\CustomJsonResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property-read User $authUser
 */
final class LogoutController extends ApiBaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->authUser->currentAccessToken()->delete();

        return CustomJsonResponse::success(statusCode: Response::HTTP_NO_CONTENT);
    }
}
