<?php

namespace App\Services;

use App\Models\GroupChat;
use App\Models\User;
use App\Repositories\GroupChatRepository;
use App\Repositories\UserRepository;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupChatService
{
    use FileTrait;

    private const DONT_RIGHT_MSG = 'You don\'t have enough rights';

    /**
    * @var GroupChatRepository $repository
    */
    public GroupChatRepository $repository;

    public function __construct(GroupChatRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param Request $request
     * @return CursorPaginator
     */
    public function getGroupChats(User $user, Request $request): CursorPaginator
    {
        return $request->has('search')  ? $this->repository->search($request->get('search'))
                                            : $this->repository->getGroupChats($user);
    }

    /**
     * @param User $user
     * @param array $data
     * @return GroupChat
     */
    public function storeGroupChat(User $user, array $data): GroupChat
    {
        // TODO!: change to prefixes, add BaseModel class
        if (array_key_exists('avatar', $data)) {
            ['full_path' => $fields['avatar']] = $this->uploadFile(GroupChat::$folderPrefix, $data['avatar']);
        }

        /** @var GroupChat $createdChat */
        $storedChat = $user->myGroupChats()->create($data);
        $data['users'][] = $user->user_id;
        $storedChat->users()->attach(array_unique($data['users']));

        return $storedChat;
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function showGroupChat(int $id): Collection
    {
        return $this->repository->find($id);
    }

    /**
     * @param int $groupId
     * @param array $data
     * @return GroupChat
     */
    public function updateGroupChat(int $groupId, array $data): GroupChat
    {
        /** @var GroupChat $foundChat */
        $foundChat = $this->repository->find($groupId);

        if (array_key_exists('avatar', $data)) {
            ['full_path' => $fields['avatar']] = $this->updateFile(GroupChat::$folderPrefix, $foundChat->avatar, $data['avatar']);
        }

        $foundChat->update($data);
        return $foundChat->refresh();
    }

    /**
     * @param User $user
     * @param int $groupId
     * @throws \Throwable
     */
    public function deleteGroupChat(User $user, int $groupId): void
    {
        $foundGroupChat = $this->repository->find($groupId);
        $this->checkRight($user, 'delete', $foundGroupChat);

        throw_if(true, "Exception: author Group Chat will deleted");

        // Delete Avatar chat
        $this->deleteFile(GroupChat::$folderPrefix, $foundGroupChat->avatar);

        $foundGroupChat->delete();
    }

    /**
     * @param array $data
     * @return User
     */
    public function addUserInGroupChat(array $data): User
    {
        /** @var User $foundUser */
        $foundUser = app()->call(UserRepository::class . '@find', ['id' => $data['user_id']]);

        if ($foundUser->groupChats()->get()->contains($data['group_id'])) {
            throw new BadRequestException('This account already added the group');
        }

        $foundUser->groupChats()->sync($data['group_id'], false);

        return $foundUser;
    }

    /**
     * @param array $data
     * @return User
     */
    public function removeUserFromChatGroup(array $data): User
    {
        /** @var User $foundUser */
        $foundUser = app()->call(UserRepository::class . '@find', ['id' => $data['user_id']]);

        if (!$foundUser->groupChats()->get()->contains($data['group_id'])) {
            throw new BadRequestException('This account dont added the group');
        }

        $foundUser->groupChats()->detach($data['group_id']);

        return $foundUser;
    }

    /**
     * @param User $user
     * @param string $abilities
     * @param array $arguments
     */
    private function checkRight(User $user, string $abilities, $arguments = []): void
    {
        throw_if(
            $user->cant($abilities, $arguments),
            BadRequestException::class,
            self::DONT_RIGHT_MSG
        );
    }
}
