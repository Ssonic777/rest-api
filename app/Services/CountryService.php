<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CountryRepository;

class CountryService
{
    /**
    * @var CountryRepository $repository
    */
    public CountryRepository $repository;

    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }
}
