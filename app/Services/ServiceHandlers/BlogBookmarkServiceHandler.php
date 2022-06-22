<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\ModelDeleteAttributes;

/**
 * class BlogBookmarkServiceHandler
 * @package App\Services\ServiceHandlers
 */
class BlogBookmarkServiceHandler extends BaseServiceHandler
{
    /**
     * @var ModelDeleteAttributes $modelDeleteAttributes
     */
    public ModelDeleteAttributes $modelDeleteAttributes;

    public function __construct(ModelDeleteAttributes $modelDeleteAttributes)
    {
        parent::__construct();
        $this->modelDeleteAttributes = $modelDeleteAttributes;
    }
}
