<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('alerts:generate')->daily();
        });
    }
}
