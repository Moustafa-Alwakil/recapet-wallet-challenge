<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Str;

trait InteractsWithIdempotency
{
    public function addIdempotencyKey(): array
    {
        return [
            'Idempotency-Key' => (string) Str::uuid(),
        ];
    }
}
