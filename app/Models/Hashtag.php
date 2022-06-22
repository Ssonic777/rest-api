<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Hashtag
 *
 * @property int $id
 * @property string $hash
 * @property string $tag
 * @property int $last_trend_time
 * @property int $trend_use_num
 * @property string|null $expire
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag newQuery()
 * @method static \Illuminate\Database\Query\Builder|Hashtag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereLastTrendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hashtag whereTrendUseNum($value)
 * @method static \Illuminate\Database\Query\Builder|Hashtag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Hashtag withoutTrashed()
 */
class Hashtag extends Model
{
    use HasFactory;

    protected $table = 'Wo_Hashtags';

    public $timestamps = false;

    public const HASHTAG_REGEX = "/#([^`~!@$%^&*\#()\-+=\\|\/\.,<>?\'\":;{}\[\]* ]+)/";

    protected $fillable = [
        'hash',
        'tag',
    ];

    /**
     * @return HasMany
     */
    public function popularityToday(): HasMany
    {
        return $this->hasMany(HashtagPopularity::class, 'hashtag_id', 'id')
            ->where('created_at', '>', now()->subDay());
    }
}
