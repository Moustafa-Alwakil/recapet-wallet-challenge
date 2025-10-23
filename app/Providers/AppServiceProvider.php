<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        //
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
            macro: fn (float $amount) => $amount * 100,
        );
    }
}
