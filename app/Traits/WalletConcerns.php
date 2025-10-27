<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\ExceptionCode;
use App\Exceptions\ConcurrentWalletTransactionException;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

trait WalletConcerns
{
    public Lock $walletLock;

    /**
     * @throws ConcurrentWalletTransactionException
     */
    public function lockWallet(int $walletId, int $seconds = 60): void
    {
        $lock = Cache::lock("wallet:$walletId:lock", $seconds);

        throw_unless(
            condition: $lock->get(),
            exception: ConcurrentWalletTransactionException::new(
                exceptionCode: ExceptionCode::CONCURRENT_WALLET_TRANSACTION,
                statusCode: Response::HTTP_UNAUTHORIZED,
                message: 'Another transaction is in progress for this wallet',
            )
        );

        $this->walletLock = $lock;
    }

    public function releaseWalletLock(): void
    {
        $this->walletLock->release();
    }
}
