<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Blog;
use App\Models\CommentLike;

/**
 * class CommentLikeObserver
 * @package App\Observer
 */
class CommentLikeObserver
{

    /**
     * Handle the CommentLike "created" event.
     *
     * @param  \App\Models\CommentLike  $commentLike
     * @return void
     */
    public function created(CommentLike $commentLike): void
    {
        if ($commentLike->article instanceof Blog && $commentLike->user->user_id != $commentLike->article->owner->user_id) {
            $data = [
                'type' => 'blockdesk_article_comment_like',
                'notifier_id' => auth()->guard('api')->id(),
                'comment_id' => $commentLike->comment_id,
                'blog_id' => $commentLike->post_id
            ];

            $commentLike->article->owner->unreadNotifications()->create($data);
        }
    }

    /**
     * Handle the CommentLike "updated" event.
     *
     * @param  \App\Models\CommentLike  $commentLike
     * @return void
     */
    public function updated(CommentLike $commentLike)
    {
        //
    }

    /**
     * Handle the CommentLike "deleted" event.
     *
     * @param  \App\Models\CommentLike  $commentLike
     * @return void
     */
    public function deleted(CommentLike $commentLike)
    {
        //
    }

    /**
     * Handle the CommentLike "restored" event.
     *
     * @param  \App\Models\CommentLike  $commentLike
     * @return void
     */
    public function restored(CommentLike $commentLike)
    {
        //
    }

    /**
     * Handle the CommentLike "force deleted" event.
     *
     * @param  \App\Models\CommentLike  $commentLike
     * @return void
     */
    public function forceDeleted(CommentLike $commentLike)
    {
        //
    }
}
