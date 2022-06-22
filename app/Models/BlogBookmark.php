<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\BlogBookmark
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark query()
 * @property int $id
 * @property int $user_id
 * @property int $blog_id
 * @property int $time
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogBookmark whereUserId($value)
 */
class BlogBookmark extends Pivot
{
    use HasFactory;

    protected $table = 'Wo_BlogsBookmarks';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'blog_id',
        'time'
    ];

    protected $perPage = 15;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
}
