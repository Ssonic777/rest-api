<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Services\LocationService;

/**
 * class LocationController
 * @package App\Http\Controllers
 */
class LocationController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var LocationService $service
     */
    private LocationService $service;

    public function __construct(LocationService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    public function searchForLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'required|string',
        ]);
        $foundLocations = $this->service->searchForLocation($validated['search']);

        return response()->json($foundLocations);
    }
}
