<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\ExceptionCode;
use App\Enums\WalletStatus;
use App\Exceptions\InactiveWalletException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureWalletIsActiveMiddleware
{
    /**
     * @throws InactiveWalletException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        throw_unless(
            condition: $currentUser->wallet->status === WalletStatus::ACTIVE,
            exception: InactiveWalletException::new(
                exceptionCode: ExceptionCode::INACTIVE_WALLET,
                statusCode: Response::HTTP_FORBIDDEN,
                message: 'Your wallet is not active.',
            )
        );

        return $next($request);
    }
}
