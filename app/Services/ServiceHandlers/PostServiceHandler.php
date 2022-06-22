<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\FileModule;
use App\Handlers\ModifyModelAttributes;
use App\Http\Resources\PostResource;
use App\Models\AlbumMedia;
use App\Models\Post;
use App\Services\Files\FileService;
use App\Services\FileTemporaryService;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Cache;

/**
 * class PostServiceHandler
 * @package App\Services\ServiceHanlders
 */
class PostServiceHandler
{
    use FileTrait;

    /**
     * @var string $filesKey
     */
    private string $filesKey = 'attachments';

    /**
     * @var array|string[] $mimeTypes
     */
    private array $mimeTypes = [
        'image/jpeg' => 'medias',
        'image/png' => 'medias',
        'image/gif' => 'medias',
        'application/zip' => 'files',
    ];

    /**
     * @var ModifyModelAttributes $modifyModelAttributes
     */
    public ModifyModelAttributes $modifyModelAttributes;

    /**
     * @var FileTemporaryService $fileTemporaryService
     */
    public FileTemporaryService $fileTemporaryService;

    /**
     * @var FileModule $fileModule
     */
    private FileModule $fileModule;

    public function __construct(
        ModifyModelAttributes $modifyModelAttributes,
        FileTemporaryService $fileTemporaryService,
        FileModule $fileModule
    ) {
        $this->modifyModelAttributes = $modifyModelAttributes;
        $this->fileTemporaryService = $fileTemporaryService;
        $this->fileModule = $fileModule;
    }

    /**
     * @param Post $post
     * @param iterable $albumMedias
     * @return void
     */
    private function savingMedias(Post $post, iterable $albumMedias): void
    {
        $col = function (Post $post, string $mediaUrl): Post {
            $postFileFields = [
                'postFile', 'postFileName'
            ];

            foreach ($postFileFields as $field) {
                $mediaUrl = ($field == 'postFileName')  ? str_replace('/' . FileService::FILE_PATH . '/', '', $mediaUrl)
                                                        : $mediaUrl;
                $post->setAttribute($field, $mediaUrl);
            }

            return $post;
        };

        foreach ($albumMedias as $media) {
            $mediaUrl = $media['full_path'];
            (count($albumMedias) == 1)  ? $col($post, $mediaUrl)->save()
                                        : $post->medias()->create(['image' => $mediaUrl]);
        }
    }

    /**
     * @param Post $post
     * @param array $fileModules
     * @return void
     */
    public function saveAttachments(Post $post, array $fileModules): void
    {
        foreach ($fileModules as $moduleName => $files) {
            if ($moduleName == 'medias') {
                $this->savingMedias($post, $files);
            }
        }
    }

    /**
     * @param Post $post
     * @param array $data
     * @return void
     */
    public function uploadAttachments(Post $post, array $data): void
    {
        if (!array_key_exists($this->filesKey, $data)) {
            return;
        }

        $albumMedias = $this->fileTemporaryService->getFiles($data[$this->filesKey]);
        $fileModules = $this->fileModule->fileModules($albumMedias, $this->mimeTypes);
        $this->saveAttachments($post, $fileModules);
        $this->fileTemporaryService->deleteFiles($data[$this->filesKey]);
    }

    /**
     * @param Post $post
     * @param array $data
     */
    public function updateAttachments(Post $post, array $data): void
    {
        if (!array_key_exists($this->filesKey, $data)) {
            return;
        }

        $this->deletingMedias($post);
        $this->uploadAttachments($post, $data);
    }


    /**
     * @param Post $post
     */
    public function deletingMedias(Post $post): void
    {
        /** @var AlbumMedia $media */
        foreach ($post->medias as $media) {
            $this->deleteFile($media->image, $media->image);
            $this->forgetFromCache($media->image);
            $media->delete();
        }
    }

    /**
     * @param string $url
     * @return bool
     */
    private function forgetFromCache(string $url): bool
    {
        return Cache::forget($url);
    }

    /**
     * @param Post $post
     */
    public function setAttachments(Post $post): void
    {
        $attachments = $this->setPostFileAttachment($post);

        if ($post->relationLoaded('medias')) {
            foreach ($post->getRelation('medias')->toArray() as ['image' => $url]) {
                $url = !filter_var($url, FILTER_VALIDATE_URL) ? sprintf('%s/%s', getenv('AWS_CDN'), $url) : $url;
                $attachments[] = $this->cacheAttachment($url);
            }
            $post->offsetUnset('medias');
        }

        $post->setAttribute('attachments', array_filter($attachments));
    }

    /**
     * @param Post $post
     * @return array
     */
    private function setPostFileAttachment(Post $post): array
    {
        $postFile = [];

        if ($post->offsetExists('postFile') && !empty($post->getRawOriginal('postFile'))) {
            $path = $post->getRawOriginal('postFile');
            $url = !filter_var($path, FILTER_VALIDATE_URL) ? sprintf('%s/%s', getenv('AWS_CDN'), $path) : $path;
            $postFile[] = $this->cacheAttachment($url);
        }
        $post->setHidden(['postFile']);

        return $postFile;
    }

    /**
     * @param Post $post
     * @return void
     */
    public function setGif(Post $post): void
    {
        if ($post->parent) {
            if (empty($post->parent->postSticker)) {
                $post->parent->setAttribute('postSticker', null);
            } else if (is_string($post->parent->postSticker)) {
                $post->parent->setAttribute('postSticker', $this->cacheAttachment($post->parent->postSticker));
            }
        }

        if (empty($post->postSticker)) {
            $post->setAttribute('postSticker', null);
        } else if (is_string($post->postSticker)) {
            $post->setAttribute('postSticker', $this->cacheAttachment($post->postSticker));
        }
    }

    /**
     * @param string $url
     * @return array|null
     */
    private function cacheAttachment(string $url): ?array
    {
        return Cache::remember($url, now()->addDay(), function () use ($url): array {
            $check = @get_headers($url);

            if (is_array($check) && stristr($check[0], '200')) {
                [0 => $width, 1 => $height, 'mime' => $mimeType] = getimagesize($url);

                return [
                    'url' => $url,
                    'width' => $width,
                    'height' => $height,
                    'mime_type' => $mimeType,
                ];
            } else {
                return [
                    'url' => 'https://www.cryptocompare.com/media/38553091/unnamed.jpg',
                    'width' => 500,
                    'height' => 500,
                    'mime_type' => 'image/jpeg',
                ];
            }
        });
    }

    /**
     * @param string $key
     * @param array $data
     * @return array
     */
    public function uploadingMedias(string $key, array $data): array
    {
        $resultMedias = [];

        if (array_key_exists($this->filesKey, $data) && count($data[$this->filesKey])) {
            foreach ($data[$key] as $key => $img) {
                ['full_path' => $resultMedias[$key]] = $this->uploadFile(Post::POST_IMAGE_PATH, $img);
            }
            unset($data[$key]);
        }

        return $resultMedias;
    }

    /**
     * @param Post $post
     * @param array $data
     * @return void
     */
    public function updatingMedias(Post $post, array $data): void
    {
        $this->deletingMedias($post);
        $this->savingMedias($post, $data);
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function parseModelAttributes(array $attributes): array
    {
        return $this->modifyModelAttributes->execute($attributes, PostResource::MODIFY_ATTRIBUTES);
    }

    /**
     * @param Post $post
     * @return void
     */
    private function setFileAttributes(Post $post): void
    {
        $this->setAttachments($post);
        $this->setGif($post);
    }

    /**
     * @param Post $post
     * @return void
     */
    public function setAttributes(Post $post): void
    {
        $this->setFileAttributes($post);
        $post->setAttribute('url', $this->getPostUrl($post->getAttribute('post_id')));

        if ($post->relationLoaded('parent') && !is_null($post->parent)) {
            $this->setAttributes($post->parent);
        }
    }

    /**
     * @param Post $post
     */
    public function setUrlAttribute(Post $post): void
    {
        $post->setAttribute('url', getenv('SITE_URL') . '/post/' . $post->post_id);
    }

    /**
     * @param int|null $postId
     * @return string|null
     */
    public function getPostUrl(?int $postId): ?string
    {
        return  !is_null($postId)   ? getenv('SITE_URL') . "/post/{$postId}"
                                    : $postId;
    }
}
