<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminConfiguration
 *
 * @property int $id
 * @property string $name
 * @property mixed $configuration
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereConfiguration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereUpdatedAt($value)
 */
class AdminConfiguration extends Model
{
    use HasFactory;

    public const TOP_TAGS_CONFIGURATION_ID = 1;

    protected $table = 'v2_admin_configurations';

    public $timestamps = false;
}
