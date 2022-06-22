<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\Base\BaseModelRepository;

class CountryRepository extends BaseModelRepository
{

    protected function getModel(): string
    {
        return Country::class;
    }
}
