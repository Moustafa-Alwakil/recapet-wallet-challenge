<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletWithdrawalRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

final class WalletWithdrawalRequest extends Model
{
    protected $fillable = [
        'amount',
        'status',
        'wallet_id',
    ];

    protected $attributes = [
        'status' => WalletWithdrawalRequestStatus::PENDING,
    ];

    protected $appends = [
        'textual_amount',
    ];

    public function getTextualAmountAttribute(): string
    {
        /** @var string $textualAmount */
        $textualAmount = Number::currency($this->amount / 100);

        return $textualAmount;
    }

    protected function casts(): array
    {
        return [
            'status' => WalletWithdrawalRequestStatus::class,
        ];
    }
}
