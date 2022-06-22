<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ModelRepositoryInterface
{
    public function get(array $columns = ["*"]): Collection;
    public function find(int $id, array $columns = ["*"]): Model;
    public function findBy(string $field, string $value, array $columns = []): Model;

    public function create(array $data): Model;
    public function update(int $id, array $data, bool $hasModelPolicy = false): Model;
    public function delete(int $id, bool $hasModelPolicy = false): bool;
}
