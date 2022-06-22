<?php

declare(strict_types=1);

namespace App\Services;

use App\Handlers\User\Attributes\UserSetIsFollowedAttributeHandler;
use App\Handlers\User\Attributes\UserSetPrivacyAttributesHandler;
use App\Handlers\User\Attributes\UserSetUrlAttributeHandler;
use App\Handlers\User\Attributes\UserSetVerifiedAttributeHandler;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Base\AuthorizeService;
use App\Services\ServiceHandlers\UserServiceHandler;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;

/**
 * class UserService
 * @package App\Services
 */
final class UserService extends AuthorizeService
{
    use FileTrait;

    /**
     * @var UserServiceHandler $handler
     */
    private UserServiceHandler $handler;

    private const SELECT_COLUMNS = [
        'first_name',
        'last_name',
        'username',
        'email',
        'country_id',
        'gender',
        'birthday',
        'about',
        'admin',
        'last_location_update',
        'website',
        'user_id',
        'avatar',
        'cover',
        'about',
        'phone_number',
        'verified',
    ];

    private const SELECT_COLUMNS_PRIVACY = [
        'follow_privacy',
        'message_privacy',
        'friend_privacy',
        'post_privacy',
        'confirm_followers',
        'group_chat_privacy',
        'show_activities_privacy',
        'status',
        'share_my_location',
        'share_my_data',
    ];

    public function __construct(UserServiceHandler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function getAuthorizeModelRepository(): string
    {
        return UserRepository::class;
    }

    /**
     * @param array|string[] $select
     * @return Collection
     */
    public function getUsers(array $select = ["*"]): Collection
    {
        return $this->repository->get();
    }

    /**
     * @param int $userId
     * @return User
     */
    public function indexUser(int $userId): User
    {
        $this->repository->setWith([
            'country',
            'field',
        ])->setWithCount([
            'followers',
            'followings',
        ]);

        /** @var User $user */
        $user = $this->repository->find($userId, self::SELECT_COLUMNS);
        $user->makeHidden('admin');
        $user->append('role');

        UserSetUrlAttributeHandler::execute($user);
        UserSetVerifiedAttributeHandler::execute($user);

        return $user;
    }

    /**
     * @param User $user
     * @param int $userId
     * @return User
     */
    public function showUser(User $user, int $userId): User
    {
        $this->repository->setWith([
            'country',
            'field',
        ])->setWithCount([
            'followers',
            'followings',
        ]);

        /** @var User $foundUser */
        $foundUser = $this->repository->find($userId, self::SELECT_COLUMNS);
        $foundUser->makeHidden('admin');
        $foundUser->append('role');

        UserSetUrlAttributeHandler::execute($foundUser);
        UserSetVerifiedAttributeHandler::execute($foundUser);
        UserSetIsFollowedAttributeHandler::execute($foundUser, $user->user_id);

        return $foundUser;
    }

    /**
     * @param int $userId
     * @return User
     */
    public function showUserPrivacy(int $userId): User
    {
        /** @var User $user */
        $user = $this->repository->find($userId, self::SELECT_COLUMNS_PRIVACY);

        UserSetPrivacyAttributesHandler::execute($user);

        return $user;
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        $modifiedData = $this->handler->modifyModelAttributes->execute($data, UserResource::MODIFY_ATTRIBUTES);

        $this->handler->updateUserMedias($modifiedData);
        $this->repository->update($user->user_id, $modifiedData);
        $this->handler->updateUserFields($user, $modifiedData);

        /** @var User $updatedUser */
        $updatedUser = $this->repository->setWith(['country', 'field'])->find($user->user_id, self::SELECT_COLUMNS);
        UserSetVerifiedAttributeHandler::execute($updatedUser);

        return $updatedUser;
    }

    /**
     * @param User $user
     * @param array $data
     * @return string
     */
    public function updateUserPrivacy(User $user, array $data): string
    {
        foreach ($data as $field => $val) {
            $data[$field] = (string)array_search($val, User::PRIVACY_FIELDS[$field]);
            $data[$field] = User::PRIVACY_FIELDS[$field][$val] ;
        }

        $this->repository->update($user->user_id, $data);
        $this->handler->updateUserFields($user, $data);

        if (
            empty(array_diff_assoc(
                $data,
                $this->repository->setSelect(self::SELECT_COLUMNS_PRIVACY)->find($user->user_id)->getAttributes()
            ))
        ) {
            $return = 'success';
        } else {
            $return = 'not update';
        }

        return $return;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        $user->authRefreshTokens()->delete();
        $this->deletingUserMedias($user);

        return $user->delete();
    }

    /**
     * @param User $user
     */
    private function deletingUserMedias(User $user): void
    {
        foreach (['avatar', 'cover'] as $value) {
            if (
                !is_null($user->$value)
                || ($value == 'avatar' && $user->avatar != User::DEFAULT_AVATAR)
                || ($value == 'cover' && $user->cover != Group::DEFAULT_COVER)
            ) {
                $this->deleteFile(User::USER_AVATAR_PATH, $user->$value);
            }
        }
    }

    /**
     * @param int $userId
     * @return CursorPaginator
     */
    public function getChats(int $userId): CursorPaginator
    {
        /** @var User $foundUser */
        $foundUser = $this->repository->find($userId);
        return $this->repository->getUserChats($foundUser->user_id);
    }
}
