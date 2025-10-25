<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\ExceptionCode;
use App\Exceptions\ImmutableLedgerModificationException;
use App\Models\WalletLedgerEntry;
use Symfony\Component\HttpFoundation\Response;

final class WalletLedgerEntryObserver
{
    /**
     * @throws ImmutableLedgerModificationException
     */
    public function updating(WalletLedgerEntry $walletLedgerEntry): void
    {
        $this->throwImmutableModificationException();
    }

    /**
     * @throws ImmutableLedgerModificationException
     */
    public function deleting(WalletLedgerEntry $walletLedgerEntry): void
    {
        $this->throwImmutableModificationException();
    }

    /**
     * @throws ImmutableLedgerModificationException
     */
    public function forceDeleting(WalletLedgerEntry $walletLedgerEntry): void
    {
        $this->throwImmutableModificationException();
    }

    /**
     * @throws ImmutableLedgerModificationException
     */
    private function throwImmutableModificationException(): void
    {
        throw ImmutableLedgerModificationException::new(
            exceptionCode: ExceptionCode::IMMUTABLE_LEDGER_MODIFICATION,
            statusCode: Response::HTTP_FORBIDDEN,
            message: 'Ledger entries are immutable and cannot be modified or deleted.',
        );
    }
}
