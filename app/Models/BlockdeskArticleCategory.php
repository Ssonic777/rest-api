<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\BlockdeskArticleCategory
 *
 * @property int $id
 * @property string $lang_key
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $Articles
 * @property-read int|null $Articles_count
 * @property-read \App\Models\Lang|null $lang
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleCategory whereLangKey($value)
 * @mixin \Eloquent
 */

/**
 * class BlockdeskArticleCategory
 * @package App\Models
 */
class BlockdeskArticleCategory extends Model
{
    use HasFactory;

    protected $table = 'Wo_Blogs_Categories';

    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'lang_key',
    ];


    /**
     * @return HasOne
     */
    public function lang(): HasOne
    {
        return $this->hasOne(Lang::class, 'id', 'lang_key');
    }
}
