<?php

namespace HasibKamal\LaravelFCM;
use Illuminate\Support\ServiceProvider;
use HasibKamal\LaravelFCM\FCM\FCMClient;

class FCMServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/fcm.php' => config_path('fcm.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(FCMClient::class, function ($app) {
            return new FCMClient();
        });
    }

}
