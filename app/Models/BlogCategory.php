<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\BlogCategory
 *
 * @property int $id
 * @property string $lang_key
 * @property-read \App\Models\Lang|null $title
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereLangKey($value)
 */
class BlogCategory extends Model
{
    use HasFactory;

    protected $table = 'Wo_Blogs_Categories';

    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function title(): HasOne
    {
        return $this->hasOne(Lang::class, 'lang_key', 'lang_key');
    }

    /**
     * @return HasMany
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category', 'id');
    }
}
