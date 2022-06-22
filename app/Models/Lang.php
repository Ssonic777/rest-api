<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Lang
 *
 * @property int $id
 * @property string $lang_key
 * @property string $type
 * @property string $english
 * @property string $french
 * @property string $german
 * @property string $italian
 * @property string $portuguese
 * @property string $russian
 * @property string $spanish
 **/
class Lang extends Model
{
    use HasFactory;

    protected $table = 'Wo_Langs';
    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'lang_key',
        'type',
        'english',
        'french',
        'german',
        'italian',
        'portuguese',
        'russian',
        'spanish',
    ];

    protected $casts = [
        'lang_key' => 'integer',
    ];

    /**
     * @return HasOne
     */
    public function groupCategory(): HasOne
    {
        return $this->hasOne(GroupCategory::class, 'lang_key', 'lang_key');
    }

    /**
     * @return HasOne
     */
    public function blogCategory(): HasOne
    {
        return $this->hasOne(BlogCategory::class, 'lang_key', 'lang_key');
    }
}
