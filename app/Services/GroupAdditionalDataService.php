<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\GroupAdditionalData;
use App\Repositories\GroupAdditionalDataRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GroupAdditionalDataService
 * @package App\Services;
 */
class GroupAdditionalDataService
{

    /**
     * @var GroupAdditionalDataRepository $repository
     */
    public GroupAdditionalDataRepository $repository;

    public function __construct(GroupAdditionalDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $groupId
     * @return Model
     */
    public function showAdditionalData(int $groupId): Model
    {
        $foundGroupAdditionalData = $this->repository->find($groupId);

        if (Gate::denies('view', $foundGroupAdditionalData)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        return $foundGroupAdditionalData;
    }

    /**
     * @param int $groupId
     * @param array $data
     * @return Model
     */
    public function updateAdditionalData(int $groupId, array $data): Model
    {
        /** @var GroupAdditionalData $foundGroupAdditionalData */
        $foundGroupAdditionalData = $this->repository->find($groupId);

        if (Gate::denies('update', $foundGroupAdditionalData)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        $foundGroupAdditionalData->update($data);

        return $foundGroupAdditionalData->refresh();
    }
}
