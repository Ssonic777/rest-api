<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Redis\FileRepository;

/**
 * class FileTemporaryService
 * @package App\Services
 */
class FileTemporaryService
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param array $UUIDs
     * @return array
     */
    public function getFiles(iterable $UUIDs): array
    {
        $albumMedias = [];

        // Get medial urls
        foreach ($UUIDs as $key => $fileUuid) {
            ['files' => $files] = $this->fileRepository->find($fileUuid);
            $albumMedias = array_merge($albumMedias, $files);
        }

        return $albumMedias;
    }

    /**
     * @param string $fileToken
     * @return void
     */
    public function deleteFile(string $fileToken): bool
    {
        return $this->fileRepository->delete($fileToken);
    }

    /**
     * @param iterable $fileTokens
     */
    public function deleteFiles(iterable $fileTokens): void
    {
        foreach ($fileTokens as $fileUuid) {
            $this->fileRepository->delete($fileUuid);
        }
    }
}
