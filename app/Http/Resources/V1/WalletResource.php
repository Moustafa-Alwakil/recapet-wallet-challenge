<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

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
            'status' => $this->status->current(),
            'balance_in_cents' => $this->balance_in_cents,
            'balance' => $this->balance,
            'textual_balance' => $this->textual_balance,
            'user_id' => $this->user_id,

            'user' => new UserResource($this->whenLoaded('user')),
            'withdrawal_requests' => WalletWithdrawalRequestResource::collection($this->whenLoaded('withdrawal_requests')),
            'deposits' => WalletDepositResource::collection($this->whenLoaded('deposits')),
            'in_transfers' => WalletTransferResource::collection($this->whenLoaded('in_transfers')),
            'out_transfers' => WalletTransferResource::collection($this->whenLoaded('out_transfers')),
            'ledger_entries' => WalletLedgerEntryResource::collection($this->whenLoaded('ledger_entries')),
        ];
    }
}
