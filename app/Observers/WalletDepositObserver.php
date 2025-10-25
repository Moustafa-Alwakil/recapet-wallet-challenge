<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\WalletDepositStatus;
use App\Enums\WalletLedgerEntryType;
use App\Models\WalletDeposit;

final class WalletDepositObserver
{
    public function updated(WalletDeposit $walletDeposit): void
    {
        if ($walletDeposit->status === WalletDepositStatus::SUCCEEDED) {
            $walletDeposit->ledger_entry()
                ->create([
                    'type' => WalletLedgerEntryType::CREDIT,
                    'amount_in_cents' => $walletDeposit->amount_in_cents,
                    'wallet_id' => $walletDeposit->wallet_id,
                ]);
        }
    }
}
