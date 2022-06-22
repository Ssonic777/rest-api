<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * class GroupRepositoryFacade
 * @package App\Facades
 */
class GroupRepositoryFacade extends Facade
{
    public const ACCESSOR = 'group_repository';

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return static::ACCESSOR;
    }
}
