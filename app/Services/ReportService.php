<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Base\RepositoryFactory;
use App\Repositories\ReportRepository;

/**
 * Class ReportService
 * @package App\Services
 */
class ReportService
{

    /**
    * @var ReportRepository $repository
    */
    public ReportRepository $repository;

    /**
     * ReportService constructor.
     * @param ReportRepository $repository
     */
    public function __construct(ReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $id
     * @param array $data
     */
    public function report(int $id, array $data): void
    {
        $typeRepository = RepositoryFactory::make($data['type']);
        $foundModel = $typeRepository->find($id);

        $data = array_merge($data, [
            "{$data['type']}_id" => $id,
            'user_id' => $foundModel->user_id,
        ]);

        $this->repository->create($data);
    }
}
