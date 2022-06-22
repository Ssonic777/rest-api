<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\Collections\PostCollectionResource;
use App\Models\User;
use App\Rules\FileExistsRule;
use App\Services\UserService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    private UserService $service;

    private PostService $postService;

    /**
    * @var User|null $authUser
    */
    private ?User $authUser;

    /**
     * @param UserService $service
     * @param PostService $postService
     */
    public function __construct(UserService $service, PostService $postService)
    {
        $this->middleware('auth:api')->except('register');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = $this->service->indexUser($this->authUser->user_id);

        return response()->json(UserResource::make($user));
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        $showUser = $this->service->showUser($this->authUser, $userId);

        return response()->json(UserResource::make($showUser));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'first_name' => 'string|min:2|max:50|alpha',
                'last_name' => 'string|min:2|max:50|alpha',
                'username' => "string|unique:Wo_Users,username,{$this->authUser->user_id},user_id",
                'email' => "email|unique:Wo_Users,email,{$this->authUser->user_id},user_id",
                'country_id' => 'nullable|integer|exists:countries,id',
                'phone_number' => 'nullable|integer',
                'gender' => 'nullable|string|in:' . implode(',', User::GENDERS),
                'birthday' => 'nullable|string|date|date_format:Y-m-d|before:' . today()->subYears(User::AGE_LIMIT)->addDay()->format('Y-m-d'),
                'about' => 'nullable|string',
                'location' => 'nullable|string',
                'website' => 'nullable|url',
                'position' => 'nullable|string|max:20',
                'avatar' => ['nullable', new FileExistsRule(User::MEDIA_MIMETYPES)],
                'cover' => ['nullable', new FileExistsRule(User::MEDIA_MIMETYPES)]
            ],
            [
                'birthday.before' => "Age must be " . User::AGE_LIMIT . " or more",
                'first_name.min' => ":attribute :min min symbols",
                'first_name.max' => ":attribute :max max symbols",
                'last_name.min' => ":attribute :min min symbols",
                'last_name.max' => ":attribute :max max symbols",
            ]
        );

        $updatedUser = $this->service->updateUser($this->authUser, $validated);

        return response()->json(UserResource::make($updatedUser));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePrivacy(Request $request): JsonResponse
    {
        $rules = [];

        foreach (User::PRIVACY_FIELDS as $key => $value) {
            $rules[$key] = 'required|string|in:' . implode(',', array_keys($value));
        }

        $validated = $request->validate($rules);

        $updatedUser = $this->service->updateUserPrivacy($this->authUser, $validated);

        return response()->json([
            'message' => $updatedUser,
        ]);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $userId): JsonResponse
    {
        $this->service->deleteUser($this->authUser);

        return response()->json([
            'message' => "User {$userId} was deleted.",
        ]);
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function getChats(int $userId): JsonResponse
    {
        $chats = $this->service->getChats($userId);

        return response()->json($chats);
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function timeline(Request $request, int $userId = null): JsonResponse
    {
        $request->merge(['user_id' => $userId])->validate([
            'user_id' => 'nullable|integer|exists:Wo_Users,user_id',
            'per_page' => 'nullable|integer|max:1000',
        ]);

        $userId ??= $this->authUser->user_id;
        $perPage = $request->query->getInt('per_page', 15);
        $timeline = $this->postService->getUserTimeline($this->authUser, $userId, $perPage);

        return response()->json(PostCollectionResource::make($timeline));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function timelinePinned(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000',
        ]);

        $perPage = $request->query->getInt('per_page', 15);
        $timelinePinned = $this->postService->getUserTimelinePinned($this->authUser, $perPage);

        return response()->json(PostCollectionResource::make($timelinePinned));
    }

    /**
     * @return JsonResponse
     */
    public function getPrivacy(): JsonResponse
    {
        $user = $this->service->showUserPrivacy($this->authUser->user_id);

        return response()->json(UserResource::make($user));
    }
}
