<?php

declare(strict_types=1);

namespace App\Collections;

use App\Collections\CollectionHandlers\BlogCollectionHandler;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Blog;

/**
 * class BlogCollection
 * @package App\Collections
 */
class BlogCollection extends Collection
{

    /**
     * @var BlogCollectionHandler $handler
     */
    private BlogCollectionHandler $handler;

    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->handler = resolve(BlogCollectionHandler::class);
    }

    /**
     * @param int|null $userId
     */
    public function setIsSavedAttribute(?int $userId): void
    {
        $this->each(function (Blog $article) use ($userId): void {
            $isSaved = $article->bookmarks()->wherePivot('user_id', $userId)->exists();
            $article->setAttribute('is_saved', $isSaved);
        });
    }

    /**
     * @param int $userId
     */
    public function setIsLikedAttribute(int $userId): void
    {
        $this->each(function (Blog $article) use ($userId): void {
            $isLiked = $article->post->reactions()->wherePivot('user_id', $userId)->exists();
            $article->setAttribute('is_liked', $isLiked);
        });
    }

    /**
     * @param array $attributes
     */
    public function deleteAttributes(array $attributes): void
    {
        $this->handler->modelAttributes->setAttributes($attributes);
        $this->each(function (Blog $article): void {
            $this->handler->modelDeleteAttributes->execute($article, $this->handler->modelAttributes);
        });
    }

    /**
     * @param int $userId
     */
    public function setIsAutorAttribute(int $userId): void
    {
        $this->each(function (Blog $article) use ($userId): void {
            $article->setAttribute('is_article_author', ($article->getRawOriginal('user') == $userId));
        });
    }

    public function setRoleAttribute(): void
    {
        $this->each(function (Blog $article): void {
            $article->owner->setAttribute('role', $article->owner->role);
            $article->owner->setHidden(['admin']);
        });
    }
}
