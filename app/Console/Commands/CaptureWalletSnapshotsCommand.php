<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class CaptureWalletSnapshotsCommand extends Command
{
    protected $signature = 'wallets:snapshot';

    protected $description = 'Capture periodic snapshots for wallets to support historical comparison and ledger auditing';

    public function handle(): void
    {
        Wallet::query()
            ->chunkById(
                count: 100,
                callback: fn (Collection $wallets) => $wallets->each(
                    fn (Wallet $wallet) => $wallet->snapshots()
                        ->create([
                            'balance_in_cents' => $wallet->balance_in_cents,
                        ])
                )
            );

    }
}
