<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Wallet;

use App\Enums\WalletStatus;
use App\Exceptions\ConcurrentWalletTransactionException;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\WalletConcerns;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @method User user($guard = null)
 */
final class TransferFromMyWalletRequest extends FormRequest
{
    use WalletConcerns;

    public Wallet $receiverUserWallet;

    public int $amountInCents;

    /**
     * @return array<string|array<mixed>>
     */
    public function rules(): array
    {
        return [
            'receiver_user_id' => [
                'required',
                Rule::exists(User::class, 'id')
                    ->whereNot('id', $this->user()->id),
            ],
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

    /**
     * @throws ValidationException
     */
    protected function passedValidation(): void
    {
        /** @var User $receiverUser */
        $receiverUser = User::query()
            ->find($this->validated('receiver_user_id'));

        throw_unless(
            condition: $receiverUser->wallet->status === WalletStatus::ACTIVE,
            exception: ValidationException::withMessages([
                'receiver_id' => 'Receiver Wallet is inactive.',
            ])
        );

        $this->receiverUserWallet = $receiverUser->wallet;
        $this->amountInCents = Number::convertToCents($this->float('amount'));
    }
}
