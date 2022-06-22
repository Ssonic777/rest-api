<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\BlogRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Blog;
use App\Collections\BlogCollection;
use App\ProjectClass\ProjectCursorPaginator;

/**
 * class BlogService
 * @package App\Services
 */
class BlogService
{
    public BlogRepository $repository;

    protected array $charMap = [
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Ū' => 'u',
        'ā' => 'a',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'ū' => 'u',
    ];

    /**
     * @param BlogRepository $repository
     */
    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User|null $user
     * @param int $limit
     * @return Collection
     */
    public function getTrendingBlockArticles(?User $user, int $limit = 4): Collection
    {
        $userId = is_null($user) ? $user : $user->user_id;
        /** @var BlogCollection $trendingBlockArticles */
        $trendingBlockArticles = $this->repository->getTrendingBlockArticles($limit);
        $trendingBlockArticles->setIsSavedAttribute($userId);

        return $this->prepareResponse($trendingBlockArticles, $userId);
    }

    /**
     * @param User|null $user
     * @return Collection
     */
    public function getPopularBlockArticles(?User $user): Collection
    {
        $userId = is_null($user) ? $user : $user->user_id;
        /** @var BlogCollection $popularBlockArticles */
        $popularBlockArticles = $this->repository->getPopularBlockArticles();
        $popularBlockArticles->setIsSavedAttribute($userId);

        return $this->prepareResponse($popularBlockArticles, $userId);
    }

    /**
     * @param User|null $user
     * @param array $filters
     * @return ProjectCursorPaginator
     */
    public function getLatestBlockArticles(?User $user, array $filters): ProjectCursorPaginator
    {
        $userId = is_null($user) ? $user : $user->user_id;
        /** @var ProjectCursorPaginator|BlogCollection $latestBlockArticles */
        $latestBlockArticles = $this->repository->getLatestBlockArticles($filters);
        $latestBlockArticles->setIsSavedAttribute($userId);
        $this->prepareResponse($latestBlockArticles->getCollection(), $userId);

        return $latestBlockArticles;
    }

    /**
     * @param User|null $user
     * @return Collection
     */
    public function getEditorsChoiceBlockArticles(?User $user): Collection
    {
        $userId = is_null($user) ? $user : $user->user_id;
        /** @var BlogCollection $editorsChoiceBlockArticles */
        $editorsChoiceBlockArticles = $this->repository->getEditorsChoiceBlockArticles();
        $editorsChoiceBlockArticles->setIsSavedAttribute($userId);

        return $this->prepareResponse($editorsChoiceBlockArticles, $userId);
    }

    /**
     * @param int $articleId
     * @param int $userId
     * @return Blog
     */
    public function getArticle(int $articleId, int $userId): Blog
    {
        $articlesCollection = new BlogCollection([$this->repository->getArticle($articleId)]);
        $articlesCollection->setIsAutorAttribute($userId);
        $articlesCollection->setRoleAttribute();
        $articlesCollection->setIsSavedAttribute($userId);
        $articlesCollection->setIsLikedAttribute($userId);
        $articlesCollection = $this->prepareResponse($articlesCollection, $userId);

        return $articlesCollection->first();
    }

    /**
     * @param User|null $user
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getMyArticles(?User $user, int $perPage = null): ProjectCursorPaginator
    {
        $userId = is_null($user) ? $user : $user->user_id;
        /** @var ProjectCursorPaginator $myArticles */
        $myArticles = $this->repository->getMyArticles($userId, $perPage);
        $myArticles->setIsSavedAttribute($userId);
        $this->prepareResponse($myArticles->getCollection(), $userId);

        return $myArticles;
    }

    /**
     * @param BlogCollection $articles
     * @param int|null $userId
     * @return BlogCollection
     */
    public function prepareResponse(BlogCollection $articles, ?int $userId): BlogCollection
    {
        $articles->each(function ($article) use ($userId): void {
            /** @var Blog $article */
            $article->setAttribute('url', $this->createUrl($article));

            $article->offsetUnset('user');

            $owner = $article->getRelation('owner');

            if (!empty($owner)) {
                $owner->setAppends([]);
                $isFollowed = $owner->followers()->wherePivot('follower_id', $userId)->exists();
                $owner->setAttribute('is_followed', $isFollowed);

                if ($owner->relationLoaded('position')) {
                    $position = $owner->getRelation('position');

                    if (!empty($position)) {
                        $owner->unsetRelation('position');
                        $owner->setAttribute('position', $position->fid_1);
                    }
                }
            }

            $category = $article->getRelation('catry');

            if (!empty($category)) {
                $category->offsetUnset('lang_key');
                $category->getRelation('title')->offsetUnset('id');
                $category->getRelation('title')->offsetUnset('type');
            }
        });

        return $articles;
    }

    /**
     * @param int $articleId
     * @return string
     */
    public function getArticleUrl(int $articleId): string
    {
        $article = $this->repository
            ->getModelClone()
            ->newQuery()
            ->select([
                'id',
                'title',
            ])->findOrFail($articleId);
        $url = $this->createUrl($article);

        return $url;
    }

    protected function createUrl(Blog $article): string
    {
        $url = $this->createUrlSlug($article->title, [
            'limit' => 80,
        ]);
        $url = getenv('SITE_URL') . "/blockdesk/{$article->id}_{$url}";

        return $url;
    }

    /**
     * @param string $input
     * @param array $options
     * @return string
     */
    protected function createUrlSlug(string $input, array $options = []): string
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $input = mb_convert_encoding($input, 'UTF-8', mb_list_encodings());
        $defaults = [
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => true
        ];
        // Merge options
        $options = array_merge($defaults, $options);
        // Make custom replacements
        $input = preg_replace(array_keys($options['replacements']), $options['replacements'], $input);
        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $input = str_replace(array_keys($this->charMap), $this->charMap, $input);
        }
        // Replace non-alphanumeric characters with our delimiter
        $input = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $input);
        // Remove duplicate delimiters
        $input = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $input);
        // Truncate slug to max. characters
        $input = mb_substr($input, 0, ($options['limit'] ? $options['limit'] : mb_strlen($input, 'UTF-8')), 'UTF-8');
        // Remove delimiter from ends
        $input = trim($input, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($input, 'UTF-8') : $input;
    }
}
