<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Base\BaseModelRepository;
use App\Models\AdminConfiguration;

/**
 * class AdminConfigurationRepository
 * @package App\Repositories;
 */
class AdminConfigurationRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    public function getModel(): string
    {
        return AdminConfiguration::class;
    }

    /**
     * @return array
     */
    public function getTopTags(): array
    {
        $adminConfiguration = $this->getModelClone()
            ->newQuery()
            ->findOrFail(AdminConfiguration::TOP_TAGS_CONFIGURATION_ID);

        return json_decode($adminConfiguration->configuration);
    }
}
