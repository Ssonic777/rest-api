<?php

declare(strict_types=1);

namespace App\Repositories\Base;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Repositories\Contracts\ModelRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class BaseModelRepository
 * @package App\Repositories\Base
 */
abstract class BaseModelRepository implements ModelRepositoryInterface
{

    /** @var Application $app */
    protected Application $app;

    /** @var Model $model */
    public Model $model;

    /** @var array|string[] $select */
    private array $select = ['*'];

    /** @var array|string[] $with  */
    private array $with = [];

    /** @var array|string[] $withCount */
    private array $withCount = [];

    /**
     * BaseModelRepository constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->initializeDefaultData();
        $this->initializeModel();
    }

    final protected function initializeModel(): void
    {
        $this->model = $this->app->make($this->getModel());
    }

    /**
     * This method for initialize default values ModelRepository
     */
    protected function initializeDefaultData(): void
    {
    }

    abstract protected function getModel(): string;

    public function getModelClone(): Model
    {
        return clone $this->model;
    }

    #region Basic methods

    /**
     * @param array|string[] $columns
     * @return Collection
     */
    public function get(array $columns = ["*"]): Collection
    {
        return $this->getModelClone()->newQuery()->get($columns);
    }

    /**
     * @param int $id
     * @param array|string[] $columns
     * @return Model
     */
    public function find(int $id, array $columns = []): Model
    {
        $columns = count($columns) ? $columns : $this->getSelect();

        return $this->getModelClone()->newQuery()
                                    ->select($columns)
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->findOrFail($id);
    }

    /**
     * @param string $field
     * @param string $value
     * @param array $columns
     * @return Model
     */
    public function findBy(string $field, string $value, array $columns = []): Model
    {
        $columns = count($columns) ? $columns : $this->getSelect();

        return $this->getModelClone()->newQuery()
                                    ->select($columns)
                                    ->where($field, $value)
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->firstOrFail();
    }

    /**
     * @param string $field
     * @param string $value
     * @return Model
     */
    public function findByOrNull(string $field, string $value): ?Model
    {
        return $this->getModelClone()->newQuery()
                    ->where($field, $value)
                    ->first();
    }

    /**
     * @param string $field
     * @param string $value
     * @param string|null $sortColumn
     * @param string $sortRule
     * @return Collection
     */
    public function getBy(string $field, string $value, string $sortColumn = null, string $sortRule = 'ASC'): Collection
    {
        $sortColumn ??= $this->model->getKeyName();

        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->where($field, $value)
                                    ->orderBy($sortColumn, $sortRule)
                                    ->withCount(['likes', 'comments'])
                                    ->with($this->getWith())
                                    ->get();
    }

    public function findWhereOrNull(array $rule): ?Model
    {
        return $this->getModelClone()->newQuery()
                                    ->where($rule)
                                    ->first();
    }
    #endregion

    #region CURD
    /**
     * @param array $data
     * @return Model
     */
    public function make(array $data): Model
    {
        return tap(
            $this->getModelClone()->newQuery()->make(),
            function (Model $model) use ($data): Model {
                $model->fill($data);
                return $model;
            }
        );
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return tap(
            $this->getModelClone()->newInstance(),
            function (Model $model) use ($data): Model {
                $model->fill($data)->save();
                return $model;
            }
        );
    }

    /**
     * @param int $id
     * @param array $data
     * @param bool $hasModelPolicy
     * @param \Closure|null $closure
     * @return Model
     */
    public function update(int $id, array $data, bool $hasModelPolicy = false, \Closure $closure = null): Model
    {
        if (array_key_exists("id", $data)) {
            unset($data["id"]);
        }

        return tap(
            $this->find($id, $this->getSelect()),
            function (Model $foundModel) use ($data, $hasModelPolicy): bool {
                if ($hasModelPolicy && Gate::denies('update', $foundModel)) {
                    throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
                }

                return $foundModel->update($data);
            }
        );
    }

    /**
     * @param int $id
     * @param bool $hasModelPolicy
     * @return bool
     */
    public function delete(int $id, bool $hasModelPolicy = false): bool
    {
        return (bool)tap(
            $this->find($id),
            function (Model $foundModel) use ($hasModelPolicy): bool {
                if ($hasModelPolicy && Gate::denies('delete', $foundModel)) {
                    throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
                }

                return $foundModel->delete();
            }
        );
    }
    #endregion

    /**
     * @param array ...$params
     * @return $this
     */
    public function setSelect(array $params = ['*']): self
    {
        $this->select = $params;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setWith(array $params = []): self
    {
        $this->with = $params;

        return $this;
    }

    /**
     * @return array|string[]
     */
    protected function getWith(): array
    {
        return $this->with;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setWithCount(array $params = []): self
    {
        $this->withCount = $params;

        return $this;
    }

    /**
     * @return array|string[]
     */
    protected function getWithCount(): array
    {
        return $this->withCount;
    }
}
