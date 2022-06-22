<?php

declare(strict_types=1);

namespace App\ProjectClass;

use App\Macros\MacrosHandlers\QueryBuilderMixinHandler;
use ArrayAccess;
use Countable;
use Illuminate\Contracts\Pagination\CursorPaginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;

/**
 * class App\ProjectClass
 * @package ProjectCursorPaginator
 */
class ProjectCursorPaginator extends AbstractCursorPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable, JsonSerializable, PaginatorContract
{
    private const QUERY_PARAMS = [
        'after',
        'before',
    ];

    private const QUERY_STRING_PARAMS = [

    ];

    /**
     * @var Request $request
     */
    private Request $request;

    /**
     * @var array $projectNextPageData
     */
    private array $projectNextPageData;

    /**
     * @var array $projectPreviousPageData
     */
    private array $projectPreviousPageData;

    /**
     * @var int|null $nextPage
     */
    private ?int $nextPage = null;

    /**
     * @var int|null $previousPage
     */
    private ?int $previousPage = null;

    /**
     * @var QueryBuilderMixinHandler $handler
     */
    private QueryBuilderMixinHandler $handler;

    /**
     * @var array $withData
     */
    private array $withData = [];

    /**
     * Indicates whether there are more items in the data source.
     *
     * @return bool
     */
    protected bool $hasMore;

    /**
     * @param $items
     * @param $perPage
     * @param null $cursor
     * @param array $options
     * @param QueryBuilderMixinHandler|null $handler
     * @param Request|null $request
     */
    public function __construct($items, $perPage, $cursor = null, array $options = [], QueryBuilderMixinHandler $handler = null, Request $request = null)
    {
        $this->options = $options;
        $this->handler = $handler;
        $this->request = $request;

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;
        $this->cursor = $cursor;
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->setNextCursorPage();
        $this->setPreviousCursorPage();

        $this->setItems($items);
    }

    /**
     * Set the items for the paginator.
     *
     * @param  mixed  $items
     * @return void
     */
    protected function setItems($items): void
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);

        $this->hasMore = $this->items->count() > $this->perPage;

        $this->items = $this->items->slice(0, $this->perPage);

        if (! is_null($this->cursor) && $this->cursor->pointsToPreviousItems()) {
            $this->items = $this->items->reverse()->values();
        }
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function links(string $view = null, $data = []): Htmlable
    {
        return $this->render($view, $data);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render($view = null, $data = []): Htmlable
    {
        return static::viewFactory()->make($view ?: Paginator::$defaultSimpleView, array_merge($data, [
            'paginator' => $this,
        ]));
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return (is_null($this->cursor) && $this->hasMore) ||
            (! is_null($this->cursor) && $this->cursor->pointsToNextItems() && $this->hasMore) ||
            (! is_null($this->cursor) && $this->cursor->pointsToPreviousItems());
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages(): bool
    {
        return ! $this->onFirstPage() || $this->hasMorePages();
    }

    /**
     * Determine if the paginator is on the first page.
     *
     * @return bool
     */
    public function onFirstPage(): bool
    {
        return is_null($this->cursor) || ($this->cursor->pointsToPreviousItems() && ! $this->hasMore);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $projectPaginateData = [
            'data' => $this->items->toArray(),
            'path' => $this->path(),
            'items_count' => $this->handler->primaryKeys->count(),
            // 'cursor_items_count' => $this->handler->foundElementsCount,
            'per_page' => $this->perPage(),
            'next_page_url' => $this->projectNextPageUrl(),
            'prev_page_url' => $this->projectPreviousPageUrl(),
            'cursor_page' => $this->handler->cursorPage,
            'next_page' => $this->nextPage,
            'prev_page' => $this->previousPage
        ];

        return array_merge($projectPaginateData, $this->projectPaginateData(), $this->withData);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @return array
     */
    private function projectPaginateData(): array
    {
        $result = [];

        foreach (self::QUERY_PARAMS as $key => $query) {
            $result[$query] = $this->request->query->has($query) ? $this->request->query->getInt($query) : null;
        }

        foreach (self::QUERY_STRING_PARAMS as $key => $query) {
            $result[$query] = $this->request->query->has($query) ? $this->request->query->get($query) : null;
        }

        return $result;
    }

    /**
     * @param array $data
     */
    protected function withData(array $data): void
    {
        $this->withData = $data;
    }

    /**
     * @return string|null
     */
    protected function projectNextPageUrl(): ?string
    {
        if (is_null($this->nextPage)) {
            return null;
        }

        $this->projectNextPageData = [
            'cursor_page' => $this->nextPage,
            'per_page' => $this->perPage,
            'after' => $this->handler->after,
            'before' => $this->handler->before
        ];

        return $this->projectUrl($this->projectNextPageData);
    }

    protected function projectPreviousPageUrl(): ?string
    {
        if (is_null($this->previousPage)) {
            return null;
        }

        $this->projectPreviousPageData = [
            'cursor_page' => $this->previousPage,
            'per_page' => $this->perPage,
            'after' => $this->handler->after,
            'before' => $this->handler->before
        ];

        return $this->projectUrl($this->projectPreviousPageData);
    }

    protected function projectUrl(array $nextOrPreviousData = []): string
    {
        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        // $parameters = is_null($cursor) ? [] : [$this->cursorName => $cursor->encode()];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, array_filter($nextOrPreviousData));
        }

        return $this->path()
            . (Str::contains($this->path(), '?') ? '&' : '?')
            . Arr::query($parameters)
            . $this->buildFragment();
    }

    private function setNextCursorPage(): void
    {
        if ($this->handler->foundElementsCount > ($this->handler->cursorPage * $this->handler->perPage)) {
            $this->nextPage = $this->handler->cursorPage + 1;
        }
    }

    private function setPreviousCursorPage(): void
    {
        if ($this->handler->cursorPage > 1) {
            $this->previousPage = $this->handler->cursorPage - 1;
        }
    }
}
