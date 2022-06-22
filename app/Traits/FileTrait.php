<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Trait FileTrait
 * @package App\Traits
 */
trait FileTrait
{
    private int $lengthRandomName = 32;

    /**
     * Upload File
     *
     * @param string $path
     * @param UploadedFile $uploadedFile
     * @param string $disk
     * @return array
     */
    protected function uploadFile(string $path, UploadedFile $uploadedFile, string $disk = 's3'): array
    {
        $this->changeDisk($disk);

        list($fileData['width'], $fileData['height']) = getimagesize($uploadedFile->getPathname());
        $fileData['path'] = $path;
        $fileData['mime_type'] = $uploadedFile->getClientMimeType();
        $fileData['type'] = $uploadedFile->getClientOriginalExtension();
        $fileData['extension'] = $uploadedFile->getClientOriginalExtension();
        $fileData['size'] = $uploadedFile->getSize();
        $fileData['user_file_name'] = $uploadedFile->getClientOriginalName();
        $fileData['name'] = Str::random($this->lengthRandomName) . '.' . $fileData['type'];
        $fileData['full_path'] = $uploadedFile->storeAs($path, $fileData['name'], ['disk' => $disk]);
        $fileData['aws_cdn'] = getenv('AWS_CDN');
        $fileData['full_cdn_path'] = $fileData['aws_cdn'] . '/' . $fileData['full_path'];

        return $fileData;
    }

    /**
     * @param string $path
     * @param string|null $deleteFileName
     * @param UploadedFile $uploadedFile
     * @param string $disk
     * @return array
     */
    protected function updateFile(string $path, ?string $deleteFileName, UploadedFile $uploadedFile, string $disk = 's3'): array
    {
        $this->deleteFile($path, $deleteFileName, $disk);

        return $this->uploadFile($path, $uploadedFile, $disk);
    }

    /**
     * @param string $path
     * @param string|null $deleteFileName
     * @param string $disk
     * @return bool
     */
    protected function deleteFile(string $path, ?string $deleteFileName, string $disk = 's3'): bool
    {
        $this->changeDisk($disk);

        if (!empty($deleteFileName) && Storage::disk($disk)->exists($path) && $this->canDeleteFile($deleteFileName)) {
            $deleteFileName = str_replace($path . "/", '', $deleteFileName);

            return Storage::disk($disk)->delete($path . '/' . $deleteFileName);
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $disk
     * @return bool
     */
    protected function deleteDir(string $path, string $disk = 's3'): bool
    {
        $this->changeDisk($disk);

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->deleteDirectory($path);

            return true;
        }

        return false;
    }

    /**
     * @param string $deleteFileName
     * @return bool
     */
    private function canDeleteFile(string $deleteFileName): bool
    {
        $defaultMedias = [
            ...Group::DEFAULT_MEDIAS
        ];

        // Is Url link
        if (filter_var($deleteFileName, FILTER_VALIDATE_URL)) {
            return false;
        }

        foreach ($defaultMedias as $value) {
            if (Str::contains($deleteFileName, $value)) {
                return false;
            }
        }

        return true;
    }

    public function changeDisk(string &$disk): void
    {
        if (config('app.env') == 'local') {
            $disk = 'public';
        }
    }
}
