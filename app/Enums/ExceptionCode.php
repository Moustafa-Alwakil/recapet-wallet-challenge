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
    case WITHDRAWAL_FAILED_DUE_TO_INSUFFICIENT_BALANCE = 4000003;
    case TRANSFER_FAILED_DUE_TO_INSUFFICIENT_BALANCE = 4000004;
    case UNAUTHORIZED = 4030001;
    case INACTIVE_WALLET = 4030002;
    case IMMUTABLE_LEDGER_MODIFICATION = 4030003;
    case ROUTE_NOT_FOUND = 4040001;
    case MODEL_NOT_FOUND = 4040002;
    case DEPOSIT_TO_WALLET_FAILED = 5000001;
    case WITHDRAW_FROM_WALLET_FAILED = 5000002;
    case TRANSFER_BETWEEN_WALLETS_FAILED = 5000003;
}
