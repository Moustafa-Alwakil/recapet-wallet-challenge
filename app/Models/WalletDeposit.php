<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletDepositStatus;
use App\Http\Resources\WalletDepositResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

/**
 * @property WalletDepositStatus $status
 * @property-read Carbon $created_at
 */
#[UseResource(WalletDepositResource::class)]
final class WalletDeposit extends Model
{
    protected $fillable = [
        'amount_in_cents',
        'status',
        'wallet_id',
    ];

    protected $appends = [
        'textual_amount',
        'amount',
    ];

    protected $attributes = [
        'status' => WalletDepositStatus::PENDING,
    ];

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_in_cents / 100;
    }

    public function getTextualAmountAttribute(): string
    {
        /** @var string $textualAmount */
        $textualAmount = Number::currency($this->amount);

        return $textualAmount;
    }

    protected function casts(): array
    {
        return [
            'status' => WalletDepositStatus::class,
        ];
    }
}
