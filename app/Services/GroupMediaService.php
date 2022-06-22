<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\Group;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\GroupRepository;
use App\Traits\FileTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GroupMediaService
 * @package App\Services
 */
class GroupMediaService
{
    use FileTrait;

    public GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param int $groupId
     * @param iterable $medias
     * @return iterable
     */
    public function updateGroupMedia(int $groupId, iterable $medias): iterable
    {
        if (Gate::denies(GatePrefixInterface::IS_GROUP_ADMIN, $groupId)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        /** @var Group $foundGroup */
        $foundGroup = $this->groupRepository->find($groupId);
        $this->updateGroupMedias($foundGroup, $medias);
        $foundGroup->update($medias);

        return $this->clientResult($medias);
    }

    /**
     * @param Group $group
     * @param iterable $medias
     * @return void
     */
    private function updateGroupMedias(Group $group, iterable &$medias): void
    {
        $pathMedias = [
            'avatar' => Group::GROUP_AVATAR,
            'cover' => Group::GROUP_COVER
        ];

        /** @var UploadedFile $media */
        foreach ($medias as $property => &$media) {
            if ($media->isFile() && $media->isValid()) {
                ['full_path' => $media] = $this->updateFile($pathMedias[$property], $group->$property, $media);
            }
        }
    }

    /**
     * @param iterable $medias
     * @return iterable
     */
    private function clientResult(iterable $medias): iterable
    {
        return array_map(fn (string $mediaUrl): string => getenv('AWS_CDN') . '/' . $mediaUrl, $medias);
    }
}
