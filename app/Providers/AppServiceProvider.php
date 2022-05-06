<?php

namespace App\Providers;

use App\Services\TwitterAPIv1CredentialsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(TwitterAPIv1CredentialsService::class, function () {
            return new TwitterAPIv1CredentialsService();
        });
    }
}
