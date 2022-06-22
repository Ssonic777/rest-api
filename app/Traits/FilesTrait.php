<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * class FilesTrait
 */
trait FilesTrait
{
    use FileTrait;

    /**
     * @param string $path
     * @param iterable $files
     * @param string $disk
     * @return array
     */
    private function uploadFiles(string $path, iterable $files, string $disk = 's3'): array
    {
        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $this->uploadFile($path, $file, $disk);
        }

        return $fileNames;
    }

    /**
     * @param string $path
     * @param iterable|null $files
     * @param string $disk
     * @return bool
     */
    private function deleteFiles(string $path, ?iterable $files, string $disk = 's3'): bool
    {
        if (!is_null($files)) {
            foreach ($files as $dFile) {
                if (is_array($dFile)) {
                    $dFile = $dFile['name'];
                }

                $this->deleteFile($path, $dFile);
            }

            return true;
        }

        return false;
    }
}
