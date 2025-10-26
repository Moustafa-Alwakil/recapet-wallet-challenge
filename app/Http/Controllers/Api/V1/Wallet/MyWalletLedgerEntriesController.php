<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Api\V1\ApiBaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property-read User $authUser
 */
final class MyWalletLedgerEntriesController extends ApiBaseController
{
    public function __invoke(Request $request): ResourceCollection
    {
        return $this->authUser
            ->wallet
            ->ledger_entries()
            ->with([
                'reference',
            ])
            ->latest()
            ->customPaginate()
            ->appends($request->query())
            ->toResourceCollection();
    }
}
