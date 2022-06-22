<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\CheckPermission;
use App\Handlers\Contracts\ModelAttributesInterface;
use App\Handlers\ModelAttributes;
use App\Handlers\ModelDeleteAttributes;

/**
 * class GroupServiceHandler
 * @package App\Services\ServiceHandlers
 */
class GroupServiceHandler
{
    /**
     * @var ModelAttributesInterface $modelAttributes
     */
    public ModelAttributesInterface $modelAttributes;

    /**
     * @var ModelDeleteAttributes $modelDeleteAttributes
     */
    public ModelDeleteAttributes $modelDeleteAttributes;

    /**
     * @var CheckPermission $checkPermission
     */
    public CheckPermission $checkPermission;

    public function __construct(
        ModelAttributes $modelAttributes,
        ModelDeleteAttributes $modelDeleteAttributes,
        CheckPermission $checkPermission
    ) {
        $this->modelAttributes = $modelAttributes;
        $this->modelDeleteAttributes = $modelDeleteAttributes;
        $this->checkPermission = $checkPermission;
    }
}
