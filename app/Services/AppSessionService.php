<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\User;
use App\Repositories\AppSessionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class AppSessionService
 * @package App\Services
 */
class AppSessionService
{
    private string $type = 'web';

    protected AppSessionRepository $repository;

    public function __construct(AppSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUserSessions(User $user): Collection
    {
        $userSessions = $this->repository->getUserSessions($user->user_id);

        return $userSessions->each->setAttribute('type', $this->type);
    }

    /**
     * @param User $user
     * @param int $id
     * @return void
     */
    public function deleteWebSession(User $user, int $id): void
    {
        $sessionToClose = $this->repository->findUserSessionById($user->user_id, $id);

        if (Gate::denies('delete', $sessionToClose)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        $sessionToClose->delete();
    }
}
