<?php

declare(strict_types=1);

namespace App\Services;

use SKAgarwal\GoogleApi\PlacesApi;
use Illuminate\Support\Collection;

/**
 * Class LocationService
 * @package App\Services
 */
class LocationService
{
    /**
     * @var ConfigService $configService
     */
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function searchForLocation(string $search): Collection
    {
        $apiKey = $this->configService->getValueByName('google_map_api');
        $googlePlaces = new PlacesApi($apiKey);

        return $googlePlaces->textSearch($search);
    }
}
