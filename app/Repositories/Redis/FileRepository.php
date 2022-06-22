<?php

declare(strict_types=1);

namespace App\Repositories\Redis;

use App\Repositories\Base\BaseRedisRepository;

/**
 * class FileRepository
 * @package App\Repositories\Redis
 */
class FileRepository extends BaseRedisRepository
{
    public function getDBName(): string
    {
        return 'file_uploads';
    }

    public function getPrefix(): string
    {
        return 'file_';
    }

    /**
     * @param string $uuid
     * @return array|null
     */
    public function find(string $uuid): ?array
    {
        $foundFiles = $this->getRedisClone()->get($uuid);

        return is_null($foundFiles) ? $foundFiles : json_decode($foundFiles, true);
    }

    /**
     * @param array $data
     * @return string
     */
    public function storeFiles(array $data): string
    {
        $data = json_encode($data, JSON_FORCE_OBJECT);
        parent::store($key = $this->getUuid(), $data, 'EX', $this->expireTTL());

        return $key;
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return string
     */
    public function update(string $uuid, array $data): string
    {
        $this->delete($uuid);
        $data = json_encode($data, JSON_FORCE_OBJECT);
        $this->getRedisClone()->set($uuid, $data, 'EX', $this->expireTTL());

        return $uuid;
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function delete(string $uuid): bool
    {
        return (bool) $this->getRedisClone()->del($this->getUuid($uuid));
    }

    /**
     * @return int
     */
    private function expireTTL(): int
    {
        return now()->addDays(7)->diffInSeconds();
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function exists(string $uuid): bool
    {
        return (bool) $this->getRedisClone()->exists($uuid);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function notExists(string $uuid): bool
    {
        return (bool) !$this->getRedisClone()->exists($uuid);
    }

    /**
     * @param string|null $file
     * @return array|null
     */
    public function pull(?string $file): ?array
    {
        $files = $file ? $this->find($file) : $file;

        if (!is_null($files)) {
            $this->delete($file);
        }

        return $files;
    }
}
