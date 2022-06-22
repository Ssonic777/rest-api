<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reaction;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * class ReactionRepository
 * @package App\Repositories
 */
class ReactionRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Reaction::class;
    }

    /**
     * @param array $data
     * @return Model
     */
    public function storeReaction(array $data): Model
    {
        return $this->create($data);
    }
}
