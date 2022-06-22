<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class BlogComment
 * @package App\Models
 */
class BlogComment extends Model
{
    use HasFactory;

    /**
     * @var string $table
     */
    protected $table = 'Wo_BlogComments';

    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'blog_id',
        'user_id',
        'text',
        'posted'
    ];

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
