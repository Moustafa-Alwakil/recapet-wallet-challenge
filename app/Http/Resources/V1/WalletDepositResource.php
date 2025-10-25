<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\WalletDeposit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletDeposit */
final class WalletDepositResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount_in_cents' => $this->amount_in_cents,
            'amount' => $this->amount,
            'textual_amount' => $this->textual_amount,
            'status' => $this->status->current(),
            'wallet_id' => $this->wallet_id,
            'created_at' => $this->created_at,

            'wallet' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }
}
