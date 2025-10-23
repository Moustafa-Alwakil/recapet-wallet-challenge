<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Wallet */
final class WalletResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance_in_cents' => $this->balance,
            'textual_balance' => $this->textual_balance,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
