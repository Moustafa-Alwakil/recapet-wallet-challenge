<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WalletWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletWithdrawalRequest */
final class WalletWithdrawalRequestResource extends JsonResource
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
            'created_at' => $this->created_at->diffForHumans(),

            'wallet' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }
}
