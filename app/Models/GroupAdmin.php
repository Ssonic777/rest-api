<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * class GroupAdmin
 * @package App\Models
 */
class GroupAdmin extends Pivot
{
    use HasFactory;

    protected $table = 'Wo_GroupAdmins';
    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'general',
        'privacy',
        'avatar',
        'members',
        'analytics',
        'delete_group',
        'deleted_at'
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        'general' => 'boolean',
        'privacy' => 'boolean',
        'avatar' => 'boolean',
        'members' => 'boolean',
        'analytics' => 'boolean',
        'delete_group' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
