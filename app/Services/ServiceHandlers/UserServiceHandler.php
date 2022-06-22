<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\Contracts\ModifyModelAttributesInterface;
use App\Handlers\ModifyModelAttributes;
use App\Handlers\User\Attributes\SetUrlAttributeHandler;
use App\Handlers\User\Attributes\SetVerifiedAttributeHandler;
use App\Http\Resources\UserFieldResource;
use App\Models\User;
use App\Services\Files\FileService;
use App\Services\FileTemporaryService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class UserServiceHandler
 * @package App\Services\ServiceHandlers
 */
class UserServiceHandler
{
    /**
     * @var ModifyModelAttributesInterface $modifyModelAttributes
     */
    public ModifyModelAttributesInterface $modifyModelAttributes;

    /**
     * @var FileTemporaryService $fileTemporaryService
     */
    public FileTemporaryService $fileTemporaryService;

    /**
     * @var FileService $fileService
     */
    public FileService $fileService;

    public function __construct(
        ModifyModelAttributes $modifyModelAttributes,
        FileTemporaryService $fileTemporaryService,
        FileService $fileService
    ) {
        $this->modifyModelAttributes = $modifyModelAttributes;
        $this->fileTemporaryService = $fileTemporaryService;
        $this->fileService = $fileService;
    }

    /**
     * @param array $data
     * @return array
     */
    public function updateUserMedias(array &$data): array
    {

        if (
            array_key_exists('avatar', $data) && array_key_exists('cover', $data)
            && $data['avatar'] == $data['cover']
            && (!is_null($data['avatar']) || !is_null($data['cover']))
        ) {
            throw new BadRequestException("avatar and cover mustn't be equals");
        }

        $mediaAttributes = [
            'avatar' => User::DEFAULT_AVATAR,
            'cover' => User::DEFAULT_COVER,
        ];

        foreach ($data as $attribute => &$value) {
            if (array_key_exists($attribute, $mediaAttributes)) {
                if (is_null($value)) {
                    $value = $mediaAttributes[$attribute];
                    continue;
                }

                $foundMedia = $this->fileService->show($value);
                $foundMedia = array_shift($foundMedia);
                $this->fileTemporaryService->deleteFile($value);
                $value = str_replace(getenv('AWS_CDN') . '/', '', $foundMedia['full_cdn_path']);
            }
        }

        return $data;
    }

    /**
     * @param User $user
     * @param array $data
     * @return void
     */
    public function updateUserFields(User $user, array $data): void
    {
        $fieldAttributes = [];

        foreach ($data as $clientProperty => $tableField) {
            if (array_key_exists($clientProperty, UserFieldResource::MODIFY_ATTRIBUTES)) {
                $key = UserFieldResource::MODIFY_ATTRIBUTES[$clientProperty];
                $fieldAttributes[$key] = $data[$clientProperty];
            }
        }

        $user->position->update($fieldAttributes);
    }
}
