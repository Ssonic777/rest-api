<?php

declare(strict_types=1);

namespace App\Handlers\Contracts;

interface CheckPermissionInterface
{
    public function setMessage(string $message): void;
    public function execute(string $ability, object ...$arguments): void;
}
