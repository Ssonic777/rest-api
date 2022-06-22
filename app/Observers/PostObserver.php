<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use App\Traits\FileTrait;

/**
 * class PostObserver
 * @package App\Observes
 */
class PostObserver
{
    use FileTrait;

    /**
     * @param Post $post
     * @return void
     */
    public function creating(Post $post): void
    {
        $post->setAttribute('postType', 'post');
    }

    /**
     * Handle the Post "created" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function created(Post $post): void
    {
        $post->post_id = $post->id;
        $post->save();
    }

    /**
     * Handle the Post "updated" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the post "deleting" event.
     *
     * @param Post $post
     * @return void
     */
    public function deleting(Post $post): void
    {
        $this->deleteFile(Post::POST_IMAGE_PATH, $post->postFile);
    }

    /**
     * Handle the Post "deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the post "saving" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function saving(Post $post): void
    {
         // $post->time = now()->format('Y-m-d H:i:s');
         $post->time = time();
    }

    /**
     * Handle the Post "restored" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
