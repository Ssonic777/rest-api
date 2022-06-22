<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Lang;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Support\Collection;

/**
 * class LangRepository
 * @package App\Repsitories
 */
class LangRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = [
        'lang_key',
        'type',
        'english'
    ];

    protected function getModel(): string
    {
        return Lang::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
    }

    /**
     * @param string $search
     * @param array $types
     * @return Collection
     */
    public function search(string $search, array $types): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->with($this->getWith())
                                    ->whereIn('type', $types)
                                    ->where('english', 'LIKE', "%{$search}%")
                                    ->get();
    }
}
