<?php

declare(strict_types=1);

namespace App\Handlers;

/**
 * class FileModule
 * @package App\Handlers
 */
class FileModule
{

    /**
     * @param iterable $files
     * @param array $mimeTypes
     * @return array
     */
    public function fileModules(iterable $files, array $mimeTypes): array
    {
        $modules = [];

        foreach ($files as $key => $file) {
            $moduleName = $mimeTypes[$file['mime_type']];
            $modules[$moduleName][] = $file;
        }

        return $modules;
    }
}
