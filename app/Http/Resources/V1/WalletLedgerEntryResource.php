<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\WalletDeposit;
use App\Models\WalletLedgerEntry;
use App\Models\WalletTransfer;
use App\Models\WalletWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WalletLedgerEntry */
final class WalletLedgerEntryResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->current(),
            'amount_in_cents' => $this->amount_in_cents,
            'amount' => $this->amount,
            'textual_amount' => $this->textual_amount,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'wallet_id' => $this->wallet_id,
            'created_at' => $this->created_at,

            'wallet' => new WalletResource($this->whenLoaded('wallet')),
            $this->reference_type => $this->guessReferenceResource(),
        ];
    }

    private function guessReferenceResource(): JsonResource
    {
        return match ($this->reference_type) {
            WalletDeposit::morphAlias() => new WalletDepositResource($this->whenLoaded('reference')),
            WalletTransfer::morphAlias() => new WalletTransferResource($this->whenLoaded('reference')),
            WalletWithdrawalRequest::morphAlias() => new WalletWithdrawalRequestResource($this->whenLoaded('reference')),
            default => new JsonResource($this->whenLoaded('reference')),
        };
    }
}
