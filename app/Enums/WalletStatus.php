<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumFeatures;

enum WalletStatus: string
{
    use EnumFeatures;

    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
}
