<?php

declare(strict_types=1);

namespace App\Rules\Report;

use App\Repositories\Base\RepositoryFactory;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ExistsDataInDataBase
 * @package App\Rules\Report
 */
class ExistsDataInDataBase implements Rule
{
    /**
    * @var string $type
    */
    private string $type;

    /**
    * @var int $value
    */
    private int $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $repository = RepositoryFactory::make($this->type);

        return $repository->exists($this->value = $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "{$this->type} by id ($this->value) not found";
    }
}
