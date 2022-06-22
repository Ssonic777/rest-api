<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Auth\Socialite\AppleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\User as OAuthTwoUser;
use SocialiteProviders\Manager\OAuth2\User;

/**
 * class AppleAuthController
 * @package App\Http\Controllers
 */
class AppleAuthController extends Controller
{

    /**
     * @var AppleAuthService $service
     */
    private AppleAuthService $service;

    /**
     * @var array|string[] $optionalFields
     */
    private array $optionalFields = [
        'user_id',
        'email',
        'first_name',
        'last_name',
    ];

    public function __construct(AppleAuthService $service)
    {
        $this->middleware('guest');
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function handle(Request $request): JsonResponse
    {
        $validationRules = [];

        foreach ($this->optionalFields as $field) {
            $validationRules[$field] = 'nullable|string';
            if ($field == 'email') {
                $validationRules[$field] .= "|$field";
            }
        }

        $request->validate(array_merge($validationRules, [
            'token' => 'required|string',
            'authorization_code' => 'required|string',
            'device_id' => 'required|string'
        ]));

        $ipAddress = $request->server->get('HTTP_X_ORIGINAL_FORWARDED_FOR', env('APP_ENV'));

        $result = $this->service->handle(
            $request->get('token'),
            $request->get('device_id'),
            $request->only($this->optionalFields),
            $ipAddress
        );

        return response()->json($result);
    }
}
