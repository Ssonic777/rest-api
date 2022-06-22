<?php

declare(strict_types=1);

namespace App\Services\Contracts\Files;

/**
 * The FileInterface for actions REDIS storage
 */
interface FileInterface
{
    public function hasFile(string $uuid): bool;
    public function find(string $uuid): string;
    public function store(): void;
    public function delete(string $uuid): void;
}
