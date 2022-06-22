<?php

declare(strict_types=1);

namespace App\Handlers;

use Illuminate\Support\Facades\Gate;
use App\Handlers\Contracts\CheckPermissionInterface;
use App\Exceptions\Contracts\ExceptionMessageInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class CheckPermission
 * @package App\Handlers
 */
class CheckPermission implements CheckPermissionInterface
{
    /**
     * @var string|null $message
     */
    private ?string $message = null;

    public function __construct()
    {
        $this->message = ExceptionMessageInterface::DONT_RIGHT_MSG;
    }

    /**
     * @param string $ability
     * @param object ...$arguments
     */
    public function execute(string $ability, object ...$arguments): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new BadRequestException($this->message);
        }
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
