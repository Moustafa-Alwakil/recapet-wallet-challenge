<?php

declare(strict_types=1);

namespace App\Enums;

enum ExceptionCode: int
{
    /**
     * Important Note:
     * Each exception code is a 7-digit number.
     * The first three digits represent the HTTP status code.
     * The last four digits indicate the sequential (ordinal) number assigned to that specific exception.
     */
    case UNCOMPLETED_SETUP = 4000001;
    case UNAUTHENTICATED = 4010001;
    case ALREADY_AUTHENTICATED = 4000002;
    case INSUFFICIENT_BALANCE = 4000003;
    case UNAUTHORIZED = 4030001;
    case ROUTE_NOT_FOUND = 4040001;
    case MODEL_NOT_FOUND = 4040002;
    case DEPOSIT_WALLET_FAILED = 5000001;
    case WITHDRAW_WALLET_FAILED = 5000002;
}
