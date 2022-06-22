<?php

namespace App\Providers;

use App\Notifications\Channels\FirebaseChanel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

/**
 * class FirebaseServiceProvider
 * @package App\Providers
 */
class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register(): void
    {
        // Register FCM Notification Chanel
        $this->app->make(ChannelManager::class)->extend('firebase', fn(): FirebaseChanel => $this->app->make(FirebaseChanel::class));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
