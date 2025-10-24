<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumFeatures;

enum WalletTransferStatus: string
{
    use EnumFeatures;

    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
