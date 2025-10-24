<?php

declare(strict_types=1);

use App\Enums\ExceptionCode;
use App\Exceptions\InternalException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\NotReportableException;
use App\Exceptions\RouteNotFoundException;
use App\Exceptions\UnauthenticatedException;
use App\Exceptions\UnauthorizedException;
use App\Http\Middleware\EnsureWalletIsActiveMiddleware;
use App\Responses\CustomJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundLaravelException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active_wallet' => EnsureWalletIsActiveMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportWhen(fn (Exception $exception) => $exception instanceof NotReportableException);

        if (request()->expectsJson()) {
            $exceptions->renderable(function (Exception $e) {
                throw_if(
                    condition: $e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundLaravelException,
                    exception: ModelNotFoundException::new(
                        exceptionCode: ExceptionCode::MODEL_NOT_FOUND,
                        statusCode: Response::HTTP_NOT_FOUND,
                        message: 'The Record You\'re looking for does not exist.',
                        description: $e->getMessage()
                    )
                );

                throw_if(
                    condition: $e instanceof NotFoundHttpException,
                    exception: RouteNotFoundException::new(
                        exceptionCode: ExceptionCode::ROUTE_NOT_FOUND,
                        statusCode: Response::HTTP_NOT_FOUND,
                        message: 'the endpoint you are looking for does not exist.',
                        description: $e->getMessage()
                    )
                );

                throw_if(
                    condition: $e instanceof AccessDeniedHttpException,
                    exception: UnauthorizedException::new(
                        exceptionCode: ExceptionCode::UNAUTHORIZED,
                        statusCode: Response::HTTP_FORBIDDEN,
                        message: 'Unauthorized.',
                        description: $e->getMessage()
                    )
                );

                throw_if(
                    condition: $e instanceof AuthenticationException,
                    exception: UnauthenticatedException::new(
                        exceptionCode: ExceptionCode::UNAUTHENTICATED,
                        statusCode: Response::HTTP_UNAUTHORIZED,
                        message: 'Unauthenticated.',
                        description: $e->getMessage(),
                    )
                );
            });

            $exceptions->renderable(
                fn (InternalException $e) => CustomJsonResponse::exception(
                    message: $e->getMessage(),
                    description: $e->getDescription(),
                    exceptionCode: $e->getExceptionCode(),
                    statusCode: $e->getCode(),
                )
            );
        }
    })->create();
