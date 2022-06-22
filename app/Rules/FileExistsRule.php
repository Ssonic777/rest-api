<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Files\FileService;
use Illuminate\Contracts\Validation\Rule;

/**
 * class FileExistsRule
 * @package App\Rules
 */
class FileExistsRule implements Rule
{

    /**
     * @var FileService $fileService
     */
    private FileService $fileService;

    /**
     * @var string|null $attribute
     */
    private ?string $attribute;

    /**
     * @var array|string[] $attributeTitles
     */
    private array $attributeTitles = [
        'post_photos' => 'photos'
    ];

    /**
     * @var string|null $notFountFileUUID
     */
    private ?string $notFoundFileUUID;

    /**
     * @var string|null $notFoundMimeType
     */
    private ?string $notFoundMimeType;

    /**
     * @var array $mimeTypes
     */
    private array $mimeTypes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $mimeTypes = [])
    {
        $this->mimeTypes = $mimeTypes;
        $this->fileService = resolve(FileService::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  array  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;

        if (is_array($value)) {
            foreach ($value as $fileUUID) {
                $foundFiles = $this->fileService->show($fileUUID);

                if (is_null($foundFiles)) {
                    $this->notFoundFileUUID = $fileUUID;

                    return false;
                }

                foreach ($foundFiles as $key => $foundFile) {
                    $foundFile = array_shift($foundFiles);
                    if (!in_array($foundFile['mime_type'], $this->mimeTypes)) {
                        $this->notFoundMimeType = $foundFile['mime_type'];

                        return false;
                    }
                }
            }
        } else {
            $foundFiles = $this->fileService->show($value);

            if (is_null($foundFiles)) {
                $this->notFoundFileUUID = $value;

                return false;
            }

            $foundFile = array_shift($foundFiles);
            if (!in_array($foundFile['mime_type'], $this->mimeTypes)) {
                $this->notFoundMimeType = $foundFile['mime_type'];

                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if (isset($this->notFoundFileUUID)) {
            $attributeTitle = $this->attributeTitle();

            return "Your uploaded {$attributeTitle} were not found file by token {$this->notFoundFileUUID}";
        }

        if (isset($this->notFoundMimeType)) {
            $attributeTitle = implode(', ', $this->mimeTypes);

            return "Your uploaded type must be {$attributeTitle}";
        }
    }

    /**
     * @return string
     */
    private function attributeTitle(): string
    {
        if (array_key_exists($this->attribute, $this->attributeTitles)) {
            return $this->attributeTitles[$this->attribute];
        }

        return $this->attribute;
    }
}
