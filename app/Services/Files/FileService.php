<?php

declare(strict_types=1);

namespace App\Services\Files;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\User;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\Redis\FileRepository;
use App\Traits\FilesTrait;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class FileService
 * @package App\Services\Files
 */
class FileService
{
    use FilesTrait;

    public const FILE_PATH = 'upload/files';

    /**
     * @var FileRepository $repository
     */
    private FileRepository $repository;

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param array $data
     * @return string
     */
    public function store(User $user, array $data): string
    {
        $result = [
            'owner_id' => $user->user_id
        ];

        if (array_key_exists('file', $data)) {
            $result['files'][] = $this->uploadFile(self::FILE_PATH, $data['file']);
        }

        if (array_key_exists('files', $data)) {
            $result['files'] = $this->uploadFiles(self::FILE_PATH, $data['files']);
        }

        return $this->repository->storeFiles($result);
    }

    /**
     * @param string $uuid
     * @return array|null
     */
    public function show(string $uuid): ?array
    {
        ['owner_id' => $ownerId, 'files' => $foundFiles] = $this->repository->find($uuid);

        $this->checkPermission(GatePrefixInterface::FILE_SHOW, $ownerId);

        return $foundFiles;
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return string|null
     */
    public function update(string $uuid, array $data): ?string
    {
        ['owner_id' => $ownerId, 'files' => $foundFiles] = $this->repository->find($uuid);

        $this->checkPermission(GatePrefixInterface::FILE_UPDATE, $ownerId);

        if ($foundFiles) {
            $this->deleteFiles(self::FILE_PATH, $foundFiles);

            if (array_key_exists('file', $data)) {
                $fileNames[] = $this->uploadFile(self::FILE_PATH, $data['file']);
            }

            if (array_key_exists('files', $data)) {
                $fileNames = $this->uploadFiles(self::FILE_PATH, $data['files']);
            }

            $data = [
                'owner_id' => $ownerId,
                'files' => $fileNames
            ];

            $foundFiles = $this->repository->update($uuid, $data);
        }

        return $foundFiles;
    }

    /**
     * @param string $uuid
     * @return bool|null
     */
    public function delete(string $uuid): ?bool
    {
        ['owner_id' => $ownerId, 'files' => $foundFiles] = $this->repository->find($uuid);

        $this->checkPermission(GatePrefixInterface::FILE_DELETE, $ownerId);

        if (is_array($foundFiles)) {
            $this->deleteFiles(self::FILE_PATH, $foundFiles);
        }

        return is_null($foundFiles) ? $foundFiles : $this->repository->delete($uuid);
    }

    /**
     * @param string $ability
     * @param int|null $ownerId
     */
    private function checkPermission(string $ability, ?int $ownerId): void
    {
        if (is_int($ownerId) && Gate::denies($ability, $ownerId)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }
    }
}
