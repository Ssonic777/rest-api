<?php

declare(strict_types=1);

namespace App\Repositories\Base;

use App\Repositories\Contracts\RedisRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use Predis\Response\Status;

/**
 * class BaseRedisRepository
 * @package App\Repositories\Base
 */
abstract class BaseRedisRepository implements RedisRepositoryInterface
{

    /** @var Application $app */
    protected Application $app;

    /**
     * @var PredisConnection $redis
     */
    protected PredisConnection $redis;

    /**
     * @var string|null $prefix
     */
    private ?string $prefix = null;

    /**
     * @var string $dbname
     */
    protected string $dbname = 'default';

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->initializeRedis();
    }

    /**
     * @return PredisConnection
     */
    protected function getRedisClone(): PredisConnection
    {
        return clone $this->redis;
    }

    private function initializeRedis(): void
    {
        $this->dbname = $this->getDBName();
        $this->prefix = $this->getPrefix();
        $this->redis = Redis::connection($this->dbname);
    }

    /**
     * @param string|null $uuid
     * @return string
     */
    protected function getUuid(string $uuid = null): string
    {
        return is_null($uuid) ? $this->prefix .= md5(uniqid(microtime())) : $uuid;
    }

    /**
     * @param string $key
     * @param string $data
     * @param string|null $expireResolution
     * @param int|null $expire
     * @param null $flag
     * @return mixed
     */
    public function store(string $key, string $data, string $expireResolution = null, int $expire = null, $flag = null): Status
    {
        return $this->getRedisClone()->set($key, $data, $expireResolution, $expire);
    }
}
