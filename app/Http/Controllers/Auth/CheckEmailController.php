<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CheckEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class CheckEmailController
 * @package App\Http\Controllers\Auth
 */
class CheckEmailController extends Controller
{
    /**
     * @var CheckEmailService $checkEmailService
     */
    private CheckEmailService $checkEmailService;

    public function __construct(CheckEmailService $checkEmailService)
    {
        $this->middleware('guest');
        $this->checkEmailService = $checkEmailService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $result = $this->checkEmailService->checkEmail($request->all());

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmailCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'email_code' => 'required|integer'
        ]);

        $result = $this->checkEmailService->checkEmailCode($validated);

        return response()->json($result);
    }
}
