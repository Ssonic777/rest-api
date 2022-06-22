<?php

declare(strict_types=1);

namespace App\Repositories\Base;

/**
 * Class RepositoryFactory
 * @package App\Repositories\Base
 */
class RepositoryFactory
{
    /**
    * @var string[] $repositories
    */
    private static array $repositories = [
        'base' => BaseModelRepository::class,
        'post' =>   \App\Repositories\PostRepository::class,
        'comment' => \App\Repositories\CommentRepository::class,
        'user' => \App\Repositories\UserRepository::class,
        'gchat' => \App\Repositories\GroupChatRepository::class,
    ];

    /**
     * @param string $type
     * @return BaseModelRepository
     */
    public static function make(string $type = 'base'): BaseModelRepository
    {
        $repositoryClass = self::$repositories[$type];

        return resolve($repositoryClass);
    }
}
