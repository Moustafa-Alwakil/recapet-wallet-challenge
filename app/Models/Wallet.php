<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Resources\WalletResource;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;

#[UseResource(WalletResource::class)]
final class Wallet extends Model
{
    protected $fillable = [
        'balance',
        'user_id',
    ];

    protected $appends = [
        'textual_balance',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTextualBalanceAttribute(): string
    {
        /** @var string $textualBalance */
        $textualBalance = Number::currency($this->balance / 100);

        return $textualBalance;
    }

    /**
     * @return HasMany<WalletWithdrawalRequest, $this>
     */
    public function withdrawal_requests(): HasMany
    {
        return $this->hasMany(WalletWithdrawalRequest::class);
    }

    public function hasSufficientBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }
}
