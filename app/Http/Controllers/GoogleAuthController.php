<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\Header\ClientHeaderInfoDTO;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class GoogleAuthController
 * @package App\Http\Controllers
 */
class GoogleAuthController extends Controller
{

    private string $deviceKey = 'device_id';
    private GoogleAuthService $service;

    public function __construct(GoogleAuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string',
            'token' => 'required|string'
        ]);

        $deviceId = $request->get('device_id');
        $ipAddress = $request->server->get('HTTP_X_ORIGINAL_FORWARDED_FOR', env('APP_ENV'));

        $clientHeaderInfoDTO = ClientHeaderInfoDTO::makeFromRequest($request);

        $result = $this->service->handle($request->get('token'), $deviceId, $ipAddress, $clientHeaderInfoDTO);

        return response()->json($result);
    }
}
