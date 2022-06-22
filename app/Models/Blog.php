<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Collections\BlogCollection;
use App\Traits\Model\DBDefault;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Blog
 *
 * @property int $id
 * @property int $user
 * @property string $title
 * @property string|null $content
 * @property string|null $description
 * @property string|null $posted
 * @property int|null $category
 * @property string|null $thumbnail
 * @property int|null $view
 * @property int|null $shared
 * @property string|null $tags
 * @property string $active
 * @property string|null $deleted_at
 * @property int $editors_choice
 * @property string|null $json_content
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereEditorsChoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereJsonContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog wherePosted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereShared($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereView($value)
 */
class Blog extends Model
{
    use HasFactory;
    use DBDefault;
    use SoftDeletes;

    const ACTIVE_STATUS = '1';

    protected $table = 'Wo_Blog';

    public $timestamps = false;

    public $perPage = 15;

    protected $casts = [
        'posted' => 'integer',
    ];

    /**
     * @param array $models
     * @return BlogCollection
     */
    public function newCollection(array $models = []): BlogCollection
    {
        return new BlogCollection($models);
    }

    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'user');
    }

    /**
     * @return HasOne
     */
    public function catry(): HasOne
    {
        return $this->hasOne(BlogCategory::class, 'id', 'category');
    }

    /**
     * @return BelongsToMany
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_BlogsBookmarks', 'blog_id', 'user_id', 'id', 'user_id');
    }

    /**
     * @param string $value
     * @return string
     */
    public function getThumbnailAttribute(string $value): string
    {
        return $this->checkDBDefaultValue('', 'thumbnail');
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function getJsonContentAttribute(?string $value): ?string
    {
        if (!empty($value)) {
            return htmlspecialchars_decode($value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return array
     */
    public function getTagsAttribute(string $value): array
    {
        return explode(',', $value);
    }

    /**
     * @return HasOne
     */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'blog_id', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Comment::class,
            Post::class,
            'blog_id',
            'post_id',
            'id',
            'post_id'
        );
    }
}
