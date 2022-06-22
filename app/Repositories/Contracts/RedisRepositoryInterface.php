<?php

namespace App\Repositories\Contracts;
namespace App\Repositories\Contracts;

interface RedisRepositoryInterface
{
    public function getDBName(): string;
    public function getPrefix(): string;
    public function pull(?string $file): ?array;
}
