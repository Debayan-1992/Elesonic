<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);
        $settings = \App\Model\Setting::findorfail(1);

        \Config::set([
            'app.name' => $settings->name,
            'app.title' => $settings->title,

            #Mail Configuration
            'mail.host' => $settings->mailhost,
            'mail.port' => $settings->mailport,
            'mail.encryption' => $settings->mailenc,
            'mail.username' => $settings->mailuser,
            'mail.password' => $settings->mailpwd,
            'mail.from.address' => $settings->mailfrom,
            'mail.from.name' => $settings->mailname,
        ]);
    }
}
