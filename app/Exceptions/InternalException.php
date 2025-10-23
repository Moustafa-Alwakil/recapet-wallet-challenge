<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ExceptionCode;
use Exception;

abstract class InternalException extends Exception
{
    protected ?string $description;

    protected ExceptionCode $exceptionCode;

    final public function __construct(
        int $statusCode,
        string $message
    ) {
        parent::__construct($message, $statusCode);
    }

    final public static function new(
        ExceptionCode $exceptionCode,
        int $statusCode,
        string $message = '',
        ?string $description = null,
    ): static {
        $exception = new static(
            $statusCode,
            $message
        );
        $exception->exceptionCode = $exceptionCode;
        $exception->description = $description;

        return $exception;
    }

    public function getDescription(): string
    {
        return $this->description ?? 'No additional description.';
    }

    public function getExceptionCode(): ExceptionCode
    {
        return $this->exceptionCode;
    }
}
