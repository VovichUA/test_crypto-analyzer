<?php

namespace App\Providers;

use App\Services\Crypto\BinanceService;
use App\Services\Crypto\BybitService;
use App\Services\Crypto\JbexService;
use App\Services\Crypto\PoloniexService;
use App\Services\Crypto\WhitebitService;
use App\Services\PairAnalyzer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PairAnalyzer::class, function () {
            return new PairAnalyzer([
                app(BinanceService::class),
                app(JbexService::class),
                app(PoloniexService::class),
                app(BybitService::class),
                app(WhitebitService::class)
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
