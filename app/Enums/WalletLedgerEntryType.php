<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumFeatures;

enum WalletLedgerEntryType: string
{
    use EnumFeatures;

    case CREDIT = 'credit';
    case DEBIT = 'debit';
    case FEE = 'fee';
}
