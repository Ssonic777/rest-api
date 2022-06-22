<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\BlogCollection;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\BlogRepository;
use App\Repositories\LangRepository;
use App\Repositories\UserRepository;
use App\Services\ServiceHandlers\BlogSearchServiceHandler;
use Illuminate\Pagination\CursorPaginator;

/**
 * class BlogSearchService
 * @package App\Services
 */
class BlogSearchService
{

    private const MIN_COUNT_SYMBOL = 3;

    /**
     * @var BlogRepository $blogRepository
     */
    private BlogRepository $blogRepository;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    /**
     * @var LangRepository $langRepository
     */
    private LangRepository $langRepository;

    /**
     * @var BlogSearchServiceHandler $handler
     */
    private BlogSearchServiceHandler $handler;

    /**
     * @var int|null $perPage
     */
    private ?int $perPage;

    public function __construct(
        BlogRepository $blogRepository,
        UserRepository $userRepository,
        LangRepository $langRepository,
        BlogSearchServiceHandler $handler
    ) {
        $this->blogRepository = $blogRepository;
        $this->userRepository = $userRepository;
        $this->langRepository = $langRepository;
        $this->handler = $handler;
    }

    /**
     * @param int|null $userId
     * @param string|null $search
     * @param int|null $perPage
     * @return ProjectCursorPaginator|null
     */
    public function search(?int $userId, string $search = null, int $perPage = null): ?ProjectCursorPaginator
    {
        if (is_null($search) || empty($search) || strlen($search) < self::MIN_COUNT_SYMBOL) {
            return null;
        }

        $this->perPage = $perPage;

        $blogCategoryIds = $this->handler->searchByLang($this->langRepository, $search);
        $this->userRepository->setSelect(['user_id']);
        $users = $this->userRepository->search($search, ['first_name', 'last_name']);
        $userIds = $users->pluck('user_id')->toArray();

        $data = [
            'columns' => ['title', 'tags'],
            'parents' => [
                'user' => [
                    'ids' => array_filter($userIds)
                ],
                'category' => [
                    'ids' => array_filter($blogCategoryIds)
                ]
            ]
        ];

        /** @var CursorPaginator $articlesCursorPaginate */
        $articlesCursorPaginate = $this->searchByBlog($search, $data);

        /** @var BlogCollection $articles */
        $articles = BlogCollection::make($articlesCursorPaginate->getCollection());
        $articles->deleteAttributes(['user']);
        $articles->setIsSavedAttribute($userId);

        return $articlesCursorPaginate->setCollection($articles);
    }

    /**
     * @param string $search
     * @param array $data
     * @return ProjectCursorPaginator
     */
    public function searchByBlog(string $search, array $data): ProjectCursorPaginator
    {
        $this->blogRepository->setSelect([
            'id',
            'category',
            'user',
            'title',
            'thumbnail',
            'tags',
            'posted'
        ]);

        $this->blogRepository->setWith([
            "owner:user_id,first_name,last_name,avatar",
            "catry.title:lang_key,english",
        ]);

        return $this->blogRepository->search(
            $search,
            $data,
            $this->perPage
        );
    }
}
