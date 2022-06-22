<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ConfigRepository;

/**
 * Class ConfigService
 * @package App\Services
 */
class ConfigService
{
    /**
     * @var ConfigRepository $repository
     */
    public ConfigRepository $repository;

    /**
     * @param ConfigRepository $repository
     */
    public function __construct(ConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getValueByName(string $name): string
    {
        return $this->repository->getValueByName($name);
    }
}
