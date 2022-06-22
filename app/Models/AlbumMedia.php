<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class AlbumMedia
 * @package App\Models
 */
class AlbumMedia extends Model
{
    use HasFactory;

    protected $table = 'Wo_Albums_Media';

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'parent_id',
        'image',
        'deleted_at'
    ];

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
