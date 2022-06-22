<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class GroupAdditionalData
 * @package App\Models
 */
class GroupAdditionalData extends Model
{
    use HasFactory;

    protected $table = 'Group_Additional_Data';

    protected $primaryKey = 'group_id';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'phone',
        'email',
        'website',
        'location',
        'facebook',
        'instagram',
        'twitter',
        'vkontakte',
        'youtube',
        'linkedin',
    ];

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
