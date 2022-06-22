<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\Contracts\ModelAttributesInterface;
use App\Handlers\ModelAttributes;

/**
 * class ServiceBaseHandler
 * @package App\Services\ServiceHandlers
 */
class BaseServiceHandler
{
    /**
     * @var ModelAttributesInterface $modelAttributes
     */
    public ModelAttributesInterface $modelAttributes;

    public function __construct()
    {
        $this->modelAttributes = resolve(ModelAttributes::class);
    }
}
