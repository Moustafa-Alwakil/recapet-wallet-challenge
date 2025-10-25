<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\FeeCalculatorContract;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletDeposit;
use App\Models\WalletLedgerEntry;
use App\Models\WalletTransfer;
use App\Models\WalletWithdrawalRequest;
use App\Services\FeeCalculatorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: FeeCalculatorContract::class,
            concrete: fn () => new FeeCalculatorService(
                staticFeeInCents: Config::integer('wallet.fee.transfer.static_in_cents'),
                percentageRate: Config::float('wallet.fee.transfer.percentage_rate'),
            )
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureUrl();
        $this->configureCommands();
        $this->configurePasswordDefaults();
        $this->configureModelRequirements();
        $this->configureNumberMacros();
        $this->configureBuilderMacros();
        $this->enforceMorphMap();
    }

    public function configureUrl(): void
    {
        URL::forceHttps(
            app()->isProduction()
        );
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    private function configurePasswordDefaults(): void
    {
        Password::defaults(
            callback: fn () => Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
        );
    }

    private function configureModelRequirements(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict();
    }

    private function configureNumberMacros(): void
    {
        Number::macro(
            name: 'convertToCents',
            macro: fn (float $amount): int => (int) ($amount * 100),
        );
    }

    private function configureBuilderMacros(): void
    {
        Builder::macro('customPaginate', fn (): LengthAwarePaginator => $this->paginate(request()->integer('per_page') ?: Config::integer('app.items_per_page')));
    }

    private function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            User::morphAlias() => User::class,
            Wallet::morphAlias() => Wallet::class,
            WalletDeposit::morphAlias() => WalletDeposit::class,
            WalletLedgerEntry::morphAlias() => WalletLedgerEntry::class,
            WalletTransfer::morphAlias() => WalletTransfer::class,
            WalletWithdrawalRequest::morphAlias() => WalletWithdrawalRequest::class,
        ]);
    }
}
