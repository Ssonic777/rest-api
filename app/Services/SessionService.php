<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\AppSessionCollection;
use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\AppSession;
use App\Models\AuthRefreshToken;
use App\Repositories\AppSessionRepository;
use App\Repositories\AuthRefreshTokenRepository;
use App\Models\User;
use App\Services\ServiceHandlers\SessionServiceHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class SessionService
 * @package App\Services
 *
 */
class SessionService
{

    private string $type = 'api';

    private AuthRefreshTokenRepository $authRefreshTokenRepository;

    private Agent $agent;

    private SessionServiceHandler $handler;

    public function __construct(
        AppSessionRepository $appSessionRepository,
        AuthRefreshTokenRepository $authRefreshTokenRepository,
        SessionServiceHandler $handler,
        Agent $agent
    ) {
        $this->authRefreshTokenRepository = $authRefreshTokenRepository;
        $this->handler = $handler;
        $this->agent = $agent;
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getUserAllSessions(User $user): Collection
    {
        // Get Web Sessions
        /** @var AppSessionCollection $webSessions */
        $webSessions = app()->call(AppSessionService::class . '@getUserSessions', ['user' => $user]);
        $convertedApiSessions = $webSessions->map(fn (AppSession $appSession): AuthRefreshToken => $this->convertAppSessionToApiSession($appSession));

        // Get API Sessions
        $apiSessions = $this->getUserSessions($user);
        $apiSessions->each->setAttribute('session_id', null);
        $result = AppSessionCollection::make($convertedApiSessions)->merge($apiSessions)->sortByDesc('updated_at');

        return $result->setAttributes();
    }

    public function getUserSessions(User $user): Collection
    {
        $this->authRefreshTokenRepository->setSelect([
            'id',
            'device_id',
            // 'user_id',
            // 'user_agent',
            // 'device',
            // 'device_type',
            'platform',
            'platform_version',
            'browser',
            'browser_version',
            'ip_address',
            // 'expire',
            // 'created_at',
            'updated_at'
        ]);
        $apiSessions = $this->authRefreshTokenRepository->getUserSessions($user->user_id);

        return $apiSessions->each->setAttribute('type', $this->type);
    }

    /**
     * @param User $user
     * @param int $id
     * @param array $data
     * @return string[]
     */
    public function delete(User $user, int $id, array $data): array
    {
        if (array_key_exists('device_id', $data) && array_key_exists('session_id', $data)) {
            throw new BadRequestException('can\'t be selected two properties device_id & session_id');
        }

        if ($data['type'] == $this->type) {
            $this->deleteApiSession($user, $id, $data['device_id']);
        } else {
            app()->call(AppSessionService::class . '@deleteWebSession', ['user' => $user, 'id' => $id]);
        }

        return ['message' => 'Session successfully closed'];
    }

    /**
     * @param User $user
     * @param int $id
     * @param string $deviceId
     * @return void
     */
    public function deleteApiSession(User $user, int $id, string $deviceId): void
    {
        $currentSession = $this->authRefreshTokenRepository->findUserSessionByDeviceId($user->user_id, $deviceId);
        $sessionToClose = $this->authRefreshTokenRepository->findUserSessionById($user->user_id, $id);

        if (Gate::denies('delete', $sessionToClose)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        if ($currentSession->device_id === $sessionToClose->device_id) {
            throw new BadRequestException("You're can't close your current session.");
        }

        \App\Services\Base\AuthRefreshTokenService::invalidateAccessToken($sessionToClose->refresh_token);
        $sessionToClose->delete();
    }

    /**
     * @param AppSession $appSession
     * @return AuthRefreshToken
     */
    private function convertAppSessionToApiSession(AppSession $appSession): AuthRefreshToken
    {
        $userAgentData = json_decode($appSession->platform_details);
        $this->agent->setUserAgent($userAgentData->userAgent);

        $attributes = [
            'id' => $appSession->id,
            'device_id' => null,
            'session_id' => $appSession->session_id,
            // 'user_agent' => $userAgentData->userAgent,
            // 'device' => $device = $this->agent->device(),
            // 'device_type' => $this->agent->deviceType(),

            'platform' => $platform = $this->agent->platform(),
            'platform_version' => $this->agent->version($platform),

            'browser' => $browser = $this->agent->browser(),
            'browser_version' => $this->agent->version($browser),
            'ip_address' => $userAgentData->ip_address,
            // 'expired_at' => $appSession->time,
            // 'created_at' => $appSession->time,
            'updated_at' => $appSession->time,
            'type' => $appSession->type
        ];

        return $this->authRefreshTokenRepository->getModelClone()->newInstance()->setRawAttributes($attributes);
    }
}
