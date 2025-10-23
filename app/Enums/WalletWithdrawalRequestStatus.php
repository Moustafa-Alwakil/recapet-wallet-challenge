<?php

declare(strict_types=1);

namespace App\Enums;

enum WalletWithdrawalRequestStatus: string
{
    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
