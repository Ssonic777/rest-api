<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\SessionCollectionResource;
use App\Models\User;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class SessionController
 * @package App\Http\Controllers
 */
class SessionController extends Controller
{
    private ?User $authUser;

    private SessionService $service;

    public function __construct(SessionService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $sessions = $this->service->getUserAllSessions($this->authUser);

        return response()->json(SessionCollectionResource::make($sessions));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): JsonResponse
    {
        //
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required_without:session_id|string|exists:auth_refresh_tokens,device_id',
            'session_id' => 'required_without:device_id|string|exists:Wo_AppsSessions,session_id',
            'type' => 'required|in:api,web'
        ]);

        $response = $this->service->delete($this->authUser, $id, $validated);

        return response()->json($response);
    }
}
