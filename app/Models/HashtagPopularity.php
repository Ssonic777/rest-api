<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\HashtagPopularity
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HashtagPopularity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HashtagPopularity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HashtagPopularity query()
 */
class HashtagPopularity extends Model
{
    use HasFactory;

    protected $table = 'v2_hashtag_popularities';
}
