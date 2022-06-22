<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\User;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\HidePostRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class PostHideService
 * @package App\Services
 */
class PostHideService
{
    /**
     * @var PostRepository $postRepository
     */
    private PostRepository $postRepository;

    /**
     * @var HidePostRepository $repository
     */
    private HidePostRepository $repository;

    public function __construct(HidePostRepository $repository, PostRepository $postRepository)
    {
        $this->repository = $repository;
        $this->postRepository = $postRepository;
    }

    /**
     * @param User $user
     * @param int $postId
     * @return array
     */
    public function hide(User $user, int $postId): array
    {
        $foundPost = $this->postRepository->find($postId);

        if (Gate::denies(GatePrefixInterface::POST_HIDE, $foundPost)) {
            throw new BadRequestException(ExceptionMessageInterface::HIDE_OWN_POST);
        }

        $message = $status = null;
        if (!$this->repository->exists($user->user_id, $postId)) {
            $user->hidePosts()->attach($postId);
            $message = 'You won’t see this post in your feed';
            $status = 'Post hidden';
        } else {
            $user->hidePosts()->detach($postId);
            $message = 'You see this post in your feed again';
            $status = 'Post unhidden';
        }

        return compact('message', 'status');
    }
}
