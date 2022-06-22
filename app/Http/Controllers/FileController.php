<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Files\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{

    /**
     * @var User $authUser
     */
    private ?User $authUser;

    /**
     * @var FileService $service
     */
    private FileService $service;

    public function __construct(FileService $service)
    {
        $this->middleware('auth:api');
        $this->service = $service;
        $this->authUser = auth()->guard('api')->user();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required_without:files|file',
            'files.*' => 'required_without:file|file'
        ]);

        $uuid = $this->service->store($this->authUser, $validated);

        return response()->json(compact('uuid'), Response::HTTP_ACCEPTED);
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $files = $this->service->show($uuid);

        return response()->json(compact('files'));
    }

    /**
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->merge(compact('uuid'))->validate([
            'uuid' => 'nullable|string',
            'file' => 'required_without:files|file',
            'files.*' => 'required_without:file|file'
        ]);

        $uuid = $this->service->update($request->get('uuid'), $validated);

        return response()->json(compact('uuid'));
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        $status = $this->service->delete($uuid);

        return response()->json(compact('status'));
    }
}
