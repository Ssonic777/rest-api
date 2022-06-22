<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupChatAdmin
 *
 * @property int|null $GroupChatID
 * @property int|null $UserID
 * @property-read \App\Models\GroupChat $chat
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin whereGroupChatID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin whereUserID($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatAdmin whereDeletedAt($value)
 */
class GroupChatAdmin extends Model
{
    use HasFactory;

    protected $table = 'Wo_GroupChatAdmins';
    protected $primaryKey = 'GroupChatID';

    public function chat()
    {
        return $this->belongsTo(GroupChat::class);
    }
}
