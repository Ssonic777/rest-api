<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Base\BaseModelRepository;
use App\Models\Config;

/**
 * Class ConfigRepository
 * @package App\Repositories
 */
class ConfigRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Config::class;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getValueByName(string $name): string
    {
        return $this->getModelClone()->newQuery()
            ->where(['name' => $name])
            ->value('value');
    }
}
