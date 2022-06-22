<?php

declare(strict_types=1);

namespace App\Policies\Gates\Contracts;

/**
 * Interface GatePrefixInterface
 * This interface for state Gate prefixes
 * @package App\Exceptions\Contracts
 */
interface GatePrefixInterface
{
    // Posts
    const POST_HIDE = 'post-hide';

    // Group Posts
    const MEMBER_CREATE_GROUP_POST = 'member-create-group-post';
    const MEMBER_UPDATE_GROUP_POST = 'member-update-group-post';
    const MEMBER_DELETE_GROUP_POST = 'member-delete-group-post';

    const GROUP_INVITE_FRIENDS = 'group-invite-friends';
    const MEMBER_LEFT_GROUP = 'member-left-group';
    const IS_GROUP_ADMIN = 'is-group-admin';

    // Files
    const FILE_SHOW = 'file-show';
    const FILE_UPDATE = 'file-update';
    const FILE_DELETE = 'file-delete';
}
