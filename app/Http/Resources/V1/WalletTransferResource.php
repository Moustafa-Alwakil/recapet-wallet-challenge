<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletTransfer */
final class WalletTransferResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount_in_cents' => $this->amount_in_cents,
            'fee_in_cents' => $this->fee_in_cents,
            'total_in_cents' => $this->total_in_cents,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'total' => $this->total,
            'textual_amount' => $this->textual_amount,
            'textual_fee' => $this->textual_fee,
            'textual_total' => $this->textual_total,
            'status' => $this->status->current(),
            'receiver_wallet_id' => $this->receiver_wallet_id,
            'sender_wallet_id' => $this->sender_wallet_id,
            'created_at' => $this->created_at,

            'receiver_wallet' => new WalletResource($this->whenLoaded('receiver_wallet')),
            'sender_wallet' => new WalletResource($this->whenLoaded('sender_wallet')),
        ];
    }
}
