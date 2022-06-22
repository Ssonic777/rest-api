<?php

declare(strict_types=1);

namespace App\Macros\MacrosHandlers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class QueryBuilderMixinHandler
 * @package App\Macros\Macros\Handlers
 */
class QueryBuilderMixinHandler
{
    /**
     * @var Builder $queryBuilder
     */
    public Builder $queryBuilder;

    /**
     * @var string|null $modelKeyName
     */
    public ?string $modelKeyName;

    /**
     * @var int|null $cursorPage
     */
    public ?int $cursorPage;

    /**
     * @var int|null $perPage
     */
    public ?int $perPage;

    /**
     * @var int|null $after
     */
    public ?int $after;

    /**
     * @var int|null $before
     */
    public ?int $before;

    /**
     * @var int|null $foundElementsCount
     */
    public ?int $foundElementsCount;

    /**
     * @var Collection $beforeIds
     */
    public Collection $beforeIds;

    /**
     * @var Collection $afterIds
     */
    public Collection $afterIds;

    /**
     * @var Collection $afterAndBeforeIds
     */
    public Collection $afterAndBeforeIds;

    /**
     * @var Collection $primaryKeys
     */
    public Collection $primaryKeys;

    /**
     * @var Collection $foundItems
     */
    public Collection $foundItems;

    /**
     * @var array $whereInKeys
     */
    public array $whereInKeys;

    /**
     * @var Collection $items
     */
    public Collection $items;

    /**
     * @var Request $request
     */
    public Request $request;

    public function __construct()
    {
        $this->request = resolve(Request::class);
    }

    public function initializeData(): void
    {
        $this->modelKeyName = $this->request->query->has('key_name') ? $this->request->query->get('key_name') : null;
        $this->cursorPage = $this->request->query->getInt('cursor_page', 1);
        $this->after = $this->request->query->getInt('after');
        $this->before = $this->request->query->getInt('before');
    }

    /**
     * @param Model $model
     * @param int|null $perPage
     * @param string|null $keyName
     * @return void
     */
    public function modelInitialize(Model $model, int $perPage = null, string $keyName = null): void
    {
        $this->modelKeyName = (empty($this->modelKeyName)) ? $keyName : $this->modelKeyName;
        $this->modelKeyName = (empty($this->modelKeyName)) ? $model->getKeyName() : $this->modelKeyName;
        $this->perPage = (!is_null($perPage) && $perPage > 0) ? $perPage : $this->request->query->getInt('per_page', $model->getPerPage());
    }

    /**
     * @param Collection $primaryCollection
     * @return void
     */
    public function setPrimaryKeys(Collection $primaryCollection): void
    {
        $this->primaryKeys = $primaryCollection;
        $this->foundElementsCount = $this->primaryKeys->count();
    }

    public function validate(): void
    {
        $this->request->validate([
            'key_name' => 'string|alpha_dash|min:2|max:10',
            'per_page' => 'integer|min:1|max:1000',
            'cursor_page' => 'integer|min:1'
        ]);
    }

    public function getAfterIds()
    {
        $isFoundAfter = false;
        $this->afterIds = collect();

        foreach ($this->primaryKeys as $id) {
            if ($this->after == $id) {
                $isFoundAfter = true;
                continue;
            }

            if ($isFoundAfter) {
                $this->afterIds->add($id);
            }
        }
    }

    public function getBeforeIds(): void
    {
        $this->beforeIds = collect();

        foreach ($this->primaryKeys as $id) {
            if ($this->before == $id) {
                break;
            }

            $this->beforeIds->add($id);
        }
    }

    public function getAfterAndBeforeIds(): void
    {
        $this->afterAndBeforeIds = collect();
        $foundAfter = $foundBefore = false;

        foreach ($this->primaryKeys as $id) {
            if ($this->after == $id) {
                $foundAfter = true;
            }

            if ($foundAfter) {
                $this->afterAndBeforeIds->add($id);
            }

            if ($this->before == $id) {
                break;
            }
        }
    }

    private function calculateAfterAndBeforeIds(): void
    {
        $this->foundElementsCount = $this->afterAndBeforeIds->count();
        $this->primaryKeys = $this->afterAndBeforeIds;
    }

    private function calculateOnlyBefore(): void
    {
        $this->foundElementsCount = $this->beforeIds->count();
        $this->primaryKeys = $this->beforeIds;
    }

    private function calculateOnlyAfter(): void
    {
        $this->foundElementsCount = $this->afterIds->count();
        $this->primaryKeys = $this->afterIds;
    }

    /**
     * @param Model $model
     * @return void
     */
    public function makeDataPaginate(Model $model): void
    {
        $show = $this->cursorPage * $this->perPage;
        $runCount = $this->primaryKeys->count() - $show;

        for ($i = 0; $i < $runCount; $i++) {
            $this->foundItems->add($model->make());
        }

        $this->items = $this->foundItems;
    }

    /**
     * @param Collection $afterOrBeforeKeys
     */
    public function getWhereInKeys(Collection $afterOrBeforeKeys): void
    {
        try {
            $primaryKeysChunk = $afterOrBeforeKeys->chunk($this->perPage);
            $index = $this->cursorPage - 1;

            $this->whereInKeys = $primaryKeysChunk->isNotEmpty() ? $primaryKeysChunk[$index]->toArray() : [];
        } catch (\Exception $ex) {
            throw new BadRequestException("Cursor page {$this->cursorPage} not found");
        }
    }

    public function projectCursorPaginateMagic(): void
    {
        if ($this->after && $this->before) {
            $this->getAfterAndBeforeIds();
            $this->calculateAfterAndBeforeIds();
            $this->getWhereInKeys($this->afterAndBeforeIds);
        } else if ($this->after) {
            $this->getAfterIds();
            $this->calculateOnlyAfter();
            $this->getWhereInKeys($this->afterIds);
        } else if ($this->before) {
            $this->getBeforeIds();
            $this->calculateOnlyBefore();
            $this->getWhereInKeys($this->beforeIds);
        } else if ($this->cursorPage) {
            $this->getWhereInKeys($this->primaryKeys);
        }
    }
}
