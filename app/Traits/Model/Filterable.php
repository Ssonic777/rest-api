<?php

declare(strict_types=1);

namespace App\Traits\Model;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Filterable
 * @package App\Traits\Model
 */
trait Filterable
{
    /**
     * Apply all relevant filters.
     *
     * @param  Builder  $query
     * @param  Filter  $filter
     * @return Builder
     */
    public function scopeFilter(Builder $query, Filter $filter): Builder
    {
        return $filter->apply($query);
    }
}
