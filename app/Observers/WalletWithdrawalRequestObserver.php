<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\WalletLedgerEntryType;
use App\Enums\WalletWithdrawalRequestStatus;
use App\Models\WalletWithdrawalRequest;

final class WalletWithdrawalRequestObserver
{
    public function updated(WalletWithdrawalRequest $walletWithdrawalRequest): void
    {
        if ($walletWithdrawalRequest->status === WalletWithdrawalRequestStatus::SUCCEEDED) {
            $walletWithdrawalRequest->ledger_entry()
                ->create([
                    'type' => WalletLedgerEntryType::DEBIT,
                    'amount_in_cents' => $walletWithdrawalRequest->amount_in_cents,
                    'wallet_id' => $walletWithdrawalRequest->wallet_id,
                ]);
        }
    }
}
