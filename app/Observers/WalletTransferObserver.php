<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\WalletLedgerEntryType;
use App\Enums\WalletTransferStatus;
use App\Models\WalletTransfer;

final class WalletTransferObserver
{
    public function updated(WalletTransfer $walletTransfer): void
    {
        if ($walletTransfer->status === WalletTransferStatus::SUCCEEDED) {
            $walletTransfer->ledger_entry()
                ->create([
                    'type' => WalletLedgerEntryType::DEBIT,
                    'amount_in_cents' => $walletTransfer->amount_in_cents,
                    'wallet_id' => $walletTransfer->sender_wallet_id,
                ]);

            if ($walletTransfer->hasFee()) {
                $walletTransfer->ledger_entry()
                    ->create([
                        'type' => WalletLedgerEntryType::FEE,
                        'amount_in_cents' => $walletTransfer->fee_in_cents,
                        'wallet_id' => $walletTransfer->sender_wallet_id,
                    ]);
            }

            $walletTransfer->ledger_entry()
                ->create([
                    'type' => WalletLedgerEntryType::CREDIT,
                    'amount_in_cents' => $walletTransfer->amount_in_cents,
                    'wallet_id' => $walletTransfer->receiver_wallet_id,
                ]);
        }
    }
}
