<?php

declare(strict_types=1);

namespace App\Providers;

use App\Macros\QueryBuilderMixin;
use Carbon\Traits\Macro;
use Illuminate\Support\ServiceProvider;

/**
 * class MacroServiceProvider
 * @package App\Providers
 */
class MacroServiceProvider extends ServiceProvider
{

    private array $mixins = [
        \Illuminate\Database\Eloquent\Builder::class => QueryBuilderMixin::class
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacros();
    }

    private function registerMacros(): void
    {
        foreach ($this->mixins as $class => $mixin) {
            /** @psalm-suppress UndefinedClass **/
            $class::mixin(resolve($mixin));
        }
    }
}
