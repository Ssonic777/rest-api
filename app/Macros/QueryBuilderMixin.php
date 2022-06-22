<?php

declare(strict_types=1);

namespace App\Macros;

use App\Macros\MacrosHandlers\QueryBuilderMixinHandler;
use App\ProjectClass\ProjectCursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;
use Closure;

/**
 * class QueryBuilderMixin
 * @package App\Macros
 * @mixin  Builder
 * @var Model $model
 */
class QueryBuilderMixin
{
    /**
     * @var QueryBuilderMixinHandler $handler
     */
    private QueryBuilderMixinHandler $handler;

    public function __construct()
    {
        $this->handler = resolve(QueryBuilderMixinHandler::class);
        $this->handler->validate();
        $this->handler->initializeData();
    }

    /**
     * @return Closure
     */
    public function cursorPaginateExtended(): Closure
    {
        return function (
            $perPage = null,
            string $whereColumn = null,
            $columns = ['*'],
            $cursorName = 'cursor',
            $cursor = null,
            int $after = null,
            int $before = null
        ): CursorPaginator {
            $whereColumn ??= "{$this->getModel()->getTable()}.{$this->getModel()->getKeyName()}";
            $before ??= request()->query->getInt('before');
            $after  ??= request()->query->getInt('after');

            if ($before && $after) {
                $this->where($whereColumn, '>=', $after);
                $this->where($whereColumn, '<=', $before);
            } elseif ($before) {
                $this->where($whereColumn, '<', $before);
            } elseif ($after) {
                $this->where($whereColumn, '>', $after);
            }

            return $this->cursorPaginate($perPage, $columns, $cursorName, $cursor);
        };
    }

    /**
     * @return Closure
     */
    public function projectCursorPaginate(): Closure
    {
        $handler = $this->handler;

        return function (int $perPage = null, string $keyName = null, string $cursorName = 'cursor', $cursor = null) use ($handler): ProjectCursorPaginator {
            $handler->modelInitialize($model = $this->getModel(), $perPage, $keyName);
            $handler->queryBuilder = $this;

            // Laravel Default Paginate Parameters
            $parameters = $this->projectCursorPaginateParameters($cursorName, $cursor);

            $handler->setPrimaryKeys($this->pluck($handler->modelKeyName));
            $handler->projectCursorPaginateMagic();
            $this->projectCursorPaginateQuery();
            $handler->makeDataPaginate($model);

            /** @var ProjectCursorPaginator $projectCP */
            $projectCP = $this->projectCP($cursorName, $cursor, $parameters);
            $this->setDataProjectCP($projectCP);

            return $projectCP;
        };
    }

    /**
     * @return Closure
     */
    public function projectCursorPaginateParameters(): Closure
    {
        return function (string $cursorName, ?Cursor $cursor): array {

            $cursor = $cursor ?: CursorPaginator::resolveCurrentCursor($cursorName);

            $orders = $this->ensureOrderForCursorPagination(! is_null($cursor) && $cursor->pointsToPreviousItems());

            $orderDirection = $orders->first()['direction'] ?? 'asc';

            $comparisonOperator = $orderDirection === 'asc' ? '>' : '<';

            $parameters = $orders->pluck('column')->toArray();

            if (! is_null($cursor)) {
                if (count($parameters) === 1) {
                    $this->where($column = $parameters[0], $comparisonOperator, $cursor->parameter($column));
                } elseif (count($parameters) > 1) {
                    $this->whereRowValues($parameters, $comparisonOperator, $cursor->parameters($parameters));
                }
            }

            return $parameters;
        };
    }

    /**
     * @return Closure
     */
    public function projectCP(): Closure
    {
        $handler = $this->handler;

        return function (string $cursorName, ?string $cursor, array $parameters) use ($handler): ProjectCursorPaginator {
            $options = [
                'path' => Paginator::resolveCurrentPath(),
                'cursorName' => $cursorName,
                'parameters' => $parameters
            ];

            /** @var ProjectCursorPaginator $cursorPaginator */
            $cursorPaginator = app()->make(
                ProjectCursorPaginator::class,
                array_merge(
                    [
                        'items' => $handler->items,
                        'perPage' => $handler->perPage
                    ],
                    compact('cursor', 'options', 'handler')
                )
            );

            return $cursorPaginator;
        };
    }

    /**
     * @return Closure
     */
    public function projectCursorPaginateQuery(): Closure
    {
        $handler = $this->handler;

        return function () use ($handler): void {
            $handler->foundItems = $handler->queryBuilder->whereIn($handler->modelKeyName, $handler->whereInKeys)->get();
            $handler->foundItems = $handler->foundItems->whereIn($handler->modelKeyName, $handler->whereInKeys);
        };
    }

    /**
     * @return Closure
     */
    public function setDataProjectCP(): Closure
    {
        $handler = $this->handler;

        return function (ProjectCursorPaginator $projectCP) use ($handler): void {
            $projectCP->appends(
                array_merge(
                    $handler->request->query->all(),
                    ['cursor_page' => $handler->cursorPage + 1]
                )
            );
        };
    }
}
