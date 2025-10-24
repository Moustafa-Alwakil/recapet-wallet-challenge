<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FeeCalculatorContract;
use Illuminate\Support\Facades\Config;

final class FeeCalculatorService implements FeeCalculatorContract
{
    private int $staticFeeInCents;

    private float $percentageRate;

    public function __construct(int $staticFeeInCents, float $percentageRate)
    {
        $this->staticFeeInCents = $staticFeeInCents;
        $this->percentageRate = $percentageRate;
    }

    public function calculate(int $amountInCents): int
    {
        return $this->shouldApplyFee($amountInCents) ? $this->applyFee($amountInCents) : 0;
    }

    private function shouldApplyFee(int $amountInCents): bool
    {
        return $amountInCents > Config::integer('wallet.fee.transfer.applicable_above_in_cents');
    }

    private function applyFee(int $amountInCents): int
    {
        return $this->staticFeeInCents + (int) ($amountInCents * $this->percentageRate);
    }
}
