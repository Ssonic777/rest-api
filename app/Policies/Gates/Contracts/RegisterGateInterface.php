<?php

declare(strict_types=1);

namespace App\Policies\Gates\Contracts;

/**
 * Interface RegisterGateInterface
 * This interface for register gates
 * @package App\Exceptions\Contracts
 */
interface RegisterGateInterface
{
    public function registerGates(): void;
}
