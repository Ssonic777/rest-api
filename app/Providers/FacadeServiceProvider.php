<?php

namespace App\Providers;

use App\Facades\GroupRepositoryFacade;
use App\Repositories\GroupRepository;
use App\Services\NotificationService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * class FacadeServiceProvider
 * @package App\Providers
 */
class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(GroupRepositoryFacade::ACCESSOR, static function (Application $app): GroupRepository {
            return new GroupRepository($app);
        });

        $this->app->bind('notify', NotificationService::class);
    }
}
