<?php

declare(strict_types=1);

namespace App\Responses;

use App\Enums\ExceptionCodeEnum;
use Illuminate\Http\JsonResponse;

final class CustomJsonResponse
{
    /**
     * @param array<mixed> $data
     */
    public static function make(string $status, ?string $message = null, ?array $data = null, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse(
            data: [
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ],
            status: $statusCode
        );
    }

    /**
     * @param array<mixed> $data
     */
    public static function success(?string $message = null, ?array $data = null, int $statusCode = 200): JsonResponse
    {
        return self::make(
            status: 'success',
            message: $message,
            data: $data,
            statusCode: $statusCode
        );
    }

    public static function exception(string $message, string $description, ExceptionCodeEnum $exceptionCode, int $statusCode = 400): JsonResponse
    {
        return new JsonResponse(
            data: [
                'status' => 'error',
                'message' => $message,
                'description' => $description,
                'code' => $exceptionCode->value,
            ],
            status: $statusCode
        );
    }
}
