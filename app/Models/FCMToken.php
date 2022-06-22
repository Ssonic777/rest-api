<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * class FCMToken
 * @package App\Models
 */
class FCMToken extends Model
{
    use HasFactory;

    protected $table = 'fcm_tokens';

    protected $fillable = [
        'user_id',
        'user_agent',
        'device_token',
        'device_id',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];
}
