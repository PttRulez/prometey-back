<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Account;
use App\Observers\AccountObserver;
use App\Models\BobReport;
use App\Observers\BobReportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Account::observe(AccountObserver::class);
        BobReport::observe(BobReportObserver::class);
    }
}
