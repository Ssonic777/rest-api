<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\Contracts\ModelDeleteAttributesInterface;
use App\Handlers\ModelDeleteAttributes;
use App\Repositories\LangRepository;

/**
 * class BlogSearchServiceHandler
 * @package App\Services\ServiceHandlers
 */
class BlogSearchServiceHandler extends BaseServiceHandler
{
    /**
     * @var ModelDeleteAttributesInterface $modelDeleteAttributes
     */
    public ModelDeleteAttributesInterface $modelDeleteAttributes;

    public function __construct(ModelDeleteAttributes $modelDeleteAttributes)
    {
        parent::__construct();
        $this->modelDeleteAttributes = $modelDeleteAttributes;
    }

    /**
     * @param LangRepository $langRepository
     * @param string $search
     * @return array
     */
    public function searchByLang(LangRepository $langRepository, string $search): array
    {
        $langRepository->setWith(['blogCategory']);
        $categories = $langRepository->search($search, ['category']);

        return $categories->pluck('blogCategory.id')->toArray();
    }
}
