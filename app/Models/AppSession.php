<?php

declare(strict_types=1);

namespace App\Models;

use App\Collections\AppSessionCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * class AppSession
 * @package App\Models
 */
class AppSession extends Model
{
    use HasFactory;

    protected $table = 'Wo_AppsSessions';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'platform',
        'platform_details',
        'time',
    ];

    /**
     * @param array $models
     * @return AppSessionCollection
     */
    public function newCollection(array $models = []): AppSessionCollection
    {
        return AppSessionCollection::make($models);
    }
}
