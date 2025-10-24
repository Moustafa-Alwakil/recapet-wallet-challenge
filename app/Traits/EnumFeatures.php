<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait EnumFeatures
{
    /** @return array<string, string|null> */
    public function current(): array
    {
        return [
            'label' => $this->label(),
            'value' => $this->value,
        ];
    }

    public function label(): string
    {
        return Str::of($this->value)->replace('_', ' ')->ucfirst()->toString();
    }
}
