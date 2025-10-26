<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Wallet;

use App\Exceptions\ConcurrentWalletTransactionException;
use App\Models\User;
use App\Traits\WalletConcerns;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Number;

/**
 * @method User user($guard = null)
 */
final class DepositToMyWalletRequest extends FormRequest
{
    use WalletConcerns;

    public int $amountInCents;

    /**
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|decimal:2|min:1|max:100000',
        ];
    }

    /**
     * @throws ConcurrentWalletTransactionException
     */
    protected function prepareForValidation(): void
    {
        $this->lockWallet($this->user()->wallet->id);
    }

    protected function passedValidation(): void
    {
        $this->amountInCents = Number::convertToCents($this->float('amount'));
    }
}
