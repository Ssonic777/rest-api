<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * class GroupMember
 * @package App\Models
 */
class GroupMember extends Pivot
{
    use HasFactory;

    public const ACTIVE = '1';
    public const NOT_ACTIVE = '0';

    public const REQUEST_ACCEPT = 'accept';
    public const REQUEST_DECLINE = 'decline';

    public const REQUEST_STATUSES = [
        self::REQUEST_ACCEPT,
        self::REQUEST_DECLINE
    ];

    protected $table = 'Wo_Group_Members';

    protected $fillable = [
        'user_id',
        'group_id',
        'time',
        'active'
    ];

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
