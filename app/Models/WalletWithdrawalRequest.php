<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletWithdrawalRequestStatus;
use App\Http\Resources\V1\WalletWithdrawalRequestResource;
use App\Observers\WalletWithdrawalRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Number;

/**
 * @property WalletWithdrawalRequestStatus $status
 */
#[ObservedBy([WalletWithdrawalRequestObserver::class])]
#[UseResource(WalletWithdrawalRequestResource::class)]
final class WalletWithdrawalRequest extends Model
{
    protected $fillable = [
        'amount_in_cents',
        'status',
        'wallet_id',
    ];

    protected $attributes = [
        'status' => WalletWithdrawalRequestStatus::PENDING,
    ];

    protected $appends = [
        'textual_amount',
        'amount',
    ];

    public static function morphAlias(): string
    {
        return 'wallet_withdrawal_request';
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

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /** @return MorphOne<WalletLedgerEntry, $this> */
    public function ledger_entry(): MorphOne
    {
        return $this->morphOne(WalletLedgerEntry::class, 'reference');
    }

    protected function casts(): array
    {
        return [
            'status' => WalletWithdrawalRequestStatus::class,
        ];
    }
}
