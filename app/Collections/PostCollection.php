<?php

declare(strict_types=1);

namespace App\Collections;

use App\Handlers\User\Attributes\UserSetIsLikedAttribute;
use App\Handlers\User\Attributes\UserSetIsPinnedAttribute;
use App\Handlers\User\Attributes\UserSetIsReportedAttribute;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * class PostCollection
 * @package App\Collection
 */
class PostCollection extends Collection
{
    /**
     * @param int $userId
     * @return $this
     */
    public function setIsLikedAttributes(int $userId): self
    {
        $this->each(fn (Post $post): Post => UserSetIsLikedAttribute::execute($post, $userId));

        return $this;
    }

    /**
     * @return $this
     */
    public function setIsPinnedAttributes(): self
    {
        $this->each(fn (Post $post): Post => UserSetIsPinnedAttribute::execute($post));

        return $this;
    }

    /**
     * @return $this
     */
    public function setIsReportedAttributes(int $userId): self
    {
        $this->each(fn (Post $post): Post => UserSetIsReportedAttribute::execute($post, $userId));

        return $this;
    }
}
