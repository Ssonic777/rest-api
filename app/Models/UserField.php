<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * class UserField
 * @package App\Models
 */
class UserField extends Model
{
    use HasFactory;

    /**
     * @var string $table
     */
    protected $table = 'Wo_UserFields';

    /**
     * @var bool $timestamps
     */
    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'user_id',
        'fid_1',
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'id',
        'user_id'
    ];
}
