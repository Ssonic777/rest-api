<?php

declare(strict_types=1);

namespace App\Exceptions\Contracts;

/**
 * Interface ExceptionMessageInterface
 * This interface for state Exception Messages
 * @package App\Exceptions\Contracts
 */
interface ExceptionMessageInterface
{
    const DONT_RIGHT_MSG = 'You don\'t have enough rights';
    const YOU_DONT_GROUP_MEMBER_MSG = 'You are not a member of this group';
    const USER_DONT_GROUP_MEMBER_MSG = 'User is not a member of this group';
    const DONT_LEFT_OWNER_GROUP = 'You will not be able to leave the group as you are the creator of this group';
    const HIDE_OWN_POST = 'You can\'t hide your post' ;
}
