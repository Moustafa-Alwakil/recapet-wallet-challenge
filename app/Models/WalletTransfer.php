<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletTransferStatus;
use App\Http\Resources\WalletTransferResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

/**
 * @property WalletTransferStatus $status
 * @property-read Carbon $created_at
 */
#[UseResource(WalletTransferResource::class)]
final class WalletTransfer extends Model
{
    protected $fillable = [
        'amount_in_cents',
        'fee_in_cents',
        'status',
        'sender_wallet_id',
        'receiver_wallet_id',
    ];

    protected $appends = [
        'textual_amount',
        'amount',
        'textual_fee',
        'fee',
        'textual_total',
        'total',
        'total_in_cents',
    ];

    protected $attributes = [
        'status' => WalletTransferStatus::PENDING,
    ];

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

    public function getFeeAttribute(): float
    {
        return $this->fee_in_cents / 100;
    }

    public function getTextualFeeAttribute(): string
    {
        /** @var string $textualFee */
        $textualFee = Number::currency($this->fee);

        return $textualFee;
    }

    public function getTotalInCentsAttribute(): int
    {
        return $this->fee_in_cents + $this->amount_in_cents;
    }

    public function getTotalAttribute(): float
    {
        return ($this->fee_in_cents + $this->amount_in_cents) / 100;
    }

    public function getTextualTotalAttribute(): string
    {
        /** @var string $total */
        $total = Number::currency($this->total);

        return $total;
    }

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function sender_wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function receiver_wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    protected function casts(): array
    {
        return [
            'status' => WalletTransferStatus::class,
        ];
    }
}
