<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReportRepository
 * @package App\Repositories
 */
class ReportRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return Report::class;
    }

    /**
     * @param int $userId
     * @param string|null $type
     * @param bool $unique
     * @return Collection
     */
    public function getReports(int $userId, string $type = null, bool $unique = false): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->when(!is_null($type), function (Builder $query) use ($type): void {
                                        $query->with("{$type}.user");
                                    })
                                    ->when($unique, function (Builder $query) use ($type): void {
                                        $query->groupBy("{$type}_id");
                                    })
                                    ->get();
    }
}
