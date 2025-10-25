<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletLedgerEntryType;
use App\Http\Resources\V1\WalletLedgerEntryResource;
use App\Observers\WalletLedgerEntryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Number;

/**
 * @property WalletLedgerEntryType $type
 */
#[ObservedBy([WalletLedgerEntryObserver::class])]
#[UseResource(WalletLedgerEntryResource::class)]
final class WalletLedgerEntry extends Model
{
    protected $fillable = [
        'type',
        'amount_in_cents',
        'reference_type',
        'reference_id',
        'wallet_id',
    ];

    protected $appends = [
        'textual_amount',
        'amount',
    ];

    public static function morphAlias(): string
    {
        return 'wallet_ledger_entry';
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

    /**
     * @return MorphTo<Model, $this>
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'type' => WalletLedgerEntryType::class,
        ];
    }
}
