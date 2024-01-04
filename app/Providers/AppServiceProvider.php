<?php

namespace App\Providers;

use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('wechat.official_account', function (Application $app) {
            $officialAccountApp = new OfficialAccountApplication(config('wechat'));
            $officialAccountApp->setRequestFromSymfonyRequest($app['request']);
            return $officialAccountApp;
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
