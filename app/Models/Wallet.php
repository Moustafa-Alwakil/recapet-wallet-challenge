<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletStatus;
use App\Http\Resources\V1\WalletResource;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;

/**
 * @property WalletStatus $status
 */
#[UseResource(WalletResource::class)]
final class Wallet extends Model
{
    protected $fillable = [
        'balance_in_cents',
        'status',
        'user_id',
    ];

    protected $appends = [
        'textual_balance',
        'balance',
    ];

    protected $attributes = [
        'status' => WalletStatus::ACTIVE,
    ];

    public static function morphAlias(): string
    {
        return 'wallet';
    }

    public function getBalanceAttribute(): float
    {
        return $this->balance_in_cents / 100;
    }

    public function getTextualBalanceAttribute(): string
    {
        /** @var string $textualBalance */
        $textualBalance = Number::currency($this->balance);

        return $textualBalance;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<WalletWithdrawalRequest, $this>
     */
    public function withdrawal_requests(): HasMany
    {
        return $this->hasMany(WalletWithdrawalRequest::class);
    }

    /**
     * @return HasMany<WalletDeposit, $this>
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(WalletDeposit::class);
    }

    /**
     * @return HasMany<WalletTransfer, $this>
     */
    public function out_transfers(): HasMany
    {
        return $this->hasMany(WalletTransfer::class, 'sender_wallet_id');
    }

    /**
     * @return HasMany<WalletTransfer, $this>
     */
    public function in_transfers(): HasMany
    {
        return $this->hasMany(WalletTransfer::class, 'receiver_wallet_id');
    }

    /**
     * @return HasMany<WalletLedgerEntry, $this>
     */
    public function wallet_ledger_entries(): HasMany
    {
        return $this->hasMany(WalletLedgerEntry::class, 'wallet_id');
    }

    public function hasSufficientBalance(int $amount): bool
    {
        return $this->balance_in_cents >= $amount;
    }

    protected function casts(): array
    {
        return [
            'status' => WalletStatus::class,
        ];
    }
}
