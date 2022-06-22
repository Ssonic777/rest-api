<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * class HidePost
 * @package App\Models
 */
class HidePost extends Pivot
{
    use HasFactory;

    public $table = 'Wo_HiddenPosts';
}
