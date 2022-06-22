<?php

declare(strict_types=1);

namespace App\Handlers\Comment;

use App\Repositories\Base\BaseRedisRepository;

/**
 * class CommentSaveAttachmentHandler
 * @package App\Handlers\Comment
 */
class CommentSaveAttachmentHandler
{
    /**
     * @param BaseRedisRepository $fileRepository
     * @param array               $data
     * @return bool
     */
    public static function execute(BaseRedisRepository $fileRepository, array &$data): bool
    {
        if (array_key_exists('file', $data)) {
            $data['c_file'] = '';
            $files = $fileRepository->pull($data['file']);

            if (!is_null($files) && is_array($files)) {
                ['full_path' => $data['c_file']] = array_shift($files['files']);

                return true;
            }
        }

        return false;
    }
}
