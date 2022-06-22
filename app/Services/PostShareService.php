<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\Post\PostRepostedEvent;
use App\Services\ServiceHandlers\PostServiceHandler;
use App\Models\Post;
use App\Models\User;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class PostShareService
 * @package App\Services
 */
class PostShareService
{
    private PostServiceHandler $handler;

    public function __construct(PostServiceHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param User $user
     * @param array $data
     * @return Post
     */
    public function sharePost(User $user, array $data): Post
    {
        $data = $this->handler->parseModelAttributes($data);

        if (!isset($data['parent_id'])) {
            throw new BadRequestException('Array $data must have \'parent_id\' key.');
        }

        $parentPost = app()->call(PostService::class . '@showPost', [
            'user' => $user,
            'postId' => $data['parent_id'],
            'fields' => [
                'postFile',
                'postFileName',
                'multi_image',
            ],
        ]);

        if (!empty($parentPost->parent_id)) {
            throw new BadRequestException('Can\'t share the shared post.');
        }

        if (!empty($parentPost->postFile)) {
            $data['postFile'] = $parentPost->postFile;
        }

        if (!empty($parentPost->postFileName)) {
            $data['postFileName'] = $parentPost->postFileName;
        }

        $data['multi_image'] = $parentPost->multi_image;

        if (!isset($data['postPrivacy'])) {
            $data['postPrivacy'] = Post::PRIVACY_EVERYONE;
        }

        $data['post_url'] = getenv('SITE_URL') . '/post/' . $parentPost->post_id;

        $sharedPost = app()->call(PostService::class . '@storePost', compact('user', 'data'));
        PostRepostedEvent::dispatch($sharedPost);

        return $sharedPost;
    }
}
