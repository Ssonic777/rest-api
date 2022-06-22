<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\ServiceProvider;

/**
 * class QueryBuilderServiceProvider
 * @package App\Providers
 */
class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Builder::macro('cursorPaginateExtended', function ($perPage = null, string $whereColumn = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null, int $after = null, int $before = null): CursorPaginator {
            $whereColumn ??= "{$this->getModel()->getTable()}.{$this->getModel()->getKeyName()}";
            $before ??= request()->query->getInt('before');
            $after  ??= request()->query->getInt('after');

            if ($before && $after) {
                $this->where($whereColumn, '>=', $after);
                $this->where($whereColumn, '<=', $before);
            } elseif ($before) {
                $this->where($whereColumn, '<', $before);
            } elseif ($after) {
                $this->where($whereColumn, '>', $after);
            }

            return $this->cursorPaginate($perPage, $columns, $cursorName, $cursor);
        });
    }
}
