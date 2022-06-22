<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Policies\Gates\Contracts\RegisterGateInterface;
use Illuminate\Support\Facades\Gate;

/**
 * class RegisterGateClasses
 * @package App\Policies\Gates
 */
class RegisterGateClasses implements RegisterGateInterface
{
    /**
     * @return string[]
     */
    public function gateList(): array
    {
        return [
            GatePrefixInterface::MEMBER_CREATE_GROUP_POST                                       => GroupPostGate::class . '@memberCreatePost',
            GatePrefixInterface::MEMBER_UPDATE_GROUP_POST                                       => GroupPostGate::class . '@memberUpdatePost',
            GatePrefixInterface::MEMBER_DELETE_GROUP_POST                                       => GroupPostGate::class . '@memberDeletePost',

            GatePrefixInterface::GROUP_INVITE_FRIENDS                                           => GroupMember::class . '@memberInviteFriends',
            GatePrefixInterface::IS_GROUP_ADMIN                                                 => GroupAdmin::class . '@isGroupAdmin',

            GatePrefixInterface::FILE_SHOW                                                      => FileGate::class . '@showFile',
            GatePrefixInterface::FILE_UPDATE                                                    => FileGate::class . '@updateFile',
            GatePrefixInterface::FILE_DELETE                                                    => FileGate::class . '@deleteFile',

            GatePrefixInterface::POST_HIDE                                                      => PostGate::class . '@hide'
        ];
    }

    public function registerGates(): void
    {
        foreach ($this->gateList() as $prefix => $gateCallback) {
            Gate::define($prefix, $gateCallback);
        }
    }
}
