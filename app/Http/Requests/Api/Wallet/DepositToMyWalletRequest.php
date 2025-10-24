<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Number;

final class DepositToMyWalletRequest extends FormRequest
{
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

    protected function passedValidation(): void
    {
        $this->amountInCents = Number::convertToCents($this->float('amount'));
    }
}
