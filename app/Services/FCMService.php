<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * class FCMService
 * @package App\Services
 */
class FCMService
{

    /**
     * @var array $notification
     */
    private array $notification;

    /**
     * @var array $data
     */
    private array $data;

    /**
     * @var array $message
     */
    private array $message;

    public function __construct()
    {
    }
}
