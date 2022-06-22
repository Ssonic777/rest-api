<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class AuthRefreshToken
 * @package App\Models
 */
class AuthRefreshToken extends Model
{
    use HasFactory;

    public $table = 'auth_refresh_tokens';

    public $fillable = [
        'user_id',
        'device_id',
        'refresh_token',
        'user_agent',
        'device',
        'device_type',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'ip_address',
        'expire'
    ];

    public $casts = [
        'expire' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
