<?php

declare(strict_types=1);

namespace App\Handlers\Contracts;

interface CanExecuteInterface
{
    public function canExecute(): bool;
}
