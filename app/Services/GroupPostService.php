<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\Post;
use App\Models\User;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GroupPostService
 * @package App\Services
 */
class GroupPostService extends PostService
{
    /**
     * @param int $groupId
     * @return Collection
     */
    public function getGroupPosts(int $groupId): Collection
    {
        $this->repository->setSelect(['post_id', 'user_id', 'postLink', 'postText', 'postPhoto', 'time']);
        $this->repository->setWith(['user:user_id,first_name,last_name,avatar,verified']);

        return $this->repository->getBy('group_id', (string)$groupId);
    }

    /**
     * @param User $user
     * @param array $data
     * @return Post
     */
    public function storeGroupPost(User $user, array $data): Post
    {
        $this->checkPermission(GatePrefixInterface::MEMBER_CREATE_GROUP_POST, $data['group_id']);

        return $this->storePost($user, $data);
    }

    /**
     * @param int $groupId
     * @param int $postId
     * @return Post
     */
    public function showGroupPost(int $groupId, int $postId): Post
    {
        $foundGroupPost = $this->repository->findGroupPost($groupId, $postId);
        $this->setAttachments($foundGroupPost);

        return $foundGroupPost;
    }

    /**
     * @param int $groupId
     * @param int $postId
     * @param array $data
     * @return Post
     */
    public function updateGroupPost(int $groupId, int $postId, array $data): Post
    {
        /** @var Post $foundGroupPost */
        $this->repository->setSelect(array_merge(PostRepository::DEFAULT_SELECT, ['group_id']));
        $foundGroupPost = $this->repository->findGroupPost($groupId, $postId);
        $this->checkPermission(GatePrefixInterface::MEMBER_UPDATE_GROUP_POST, $foundGroupPost);
        $foundGroupPost->update($this->parseModelAttributes($data));
        $this->updateAttachments($foundGroupPost, $data);
        /** @var Post $foundGroupPost */
        $foundGroupPost = $this->repository->findGroupPost($groupId, $postId);
        $this->setAttachments($foundGroupPost);

        return $foundGroupPost;
    }

    /**
     * @param int $groupId
     * @param int $postId
     */
    public function deleteGroupPost(int $groupId, int $postId): void
    {
        /** @var Post $foundGroupPost */
        $foundGroupPost = $this->repository->findGroupPost($groupId, $postId);
        $this->checkPermission(GatePrefixInterface::MEMBER_DELETE_GROUP_POST, $foundGroupPost);
        $this->deletingMedias($foundGroupPost);
        $foundGroupPost->delete();
    }

    /**
     * @param string $ability
     * @param ...$arguments
     */
    private function checkPermission(string $ability, ...$arguments): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }
    }
}
