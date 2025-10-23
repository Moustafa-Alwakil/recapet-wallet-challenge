<?php

declare(strict_types=1);

namespace App\Enums;

enum ExceptionCodeEnum: int
{
    /**
     * Important Note:
     * Each exception code is a 7-digit number.
     * The first three digits represent the HTTP status code.
     * The last four digits indicate the sequential (ordinal) number assigned to that specific exception.
     */
}
