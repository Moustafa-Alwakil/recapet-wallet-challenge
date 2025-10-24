<?php

declare(strict_types=1);

namespace App\Contracts;

interface FeeCalculatorContract
{
    public function __construct(int $staticFeeInCents, float $percentageRate);

    public function calculate(int $amountInCents): int;
}
