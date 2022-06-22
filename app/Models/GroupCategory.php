<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GroupCategory
 *
 * @property int $id
 * @property string $lang_key
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\Lang|null $lang
 * @method static \Illuminate\Database\Eloquent\Builder|GroupCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupCategory whereLangKey($value)
 * @mixin \Eloquent
 */

/**
 * class GroupCategory
 * @package App\Models
 */
class GroupCategory extends Model
{
    use HasFactory;

    protected $table = 'Wo_Groups_Categories';

    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'lang_key',
    ];

    /**
     * @return HasMany
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'category', 'id');
    }

    /**
     * @return HasOne
     */
    public function lang(): HasOne
    {
        return $this->hasOne(Lang::class, 'lang_key', 'lang_key');
    }
}
