<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class HeaderController
 * @package App\Http\Controllers
 */
class HeaderController extends Controller
{
    /**
     * @var array|string[] $data
     */
    private array $data = [
        'x-os',
        'x-os-version',
        'x-app-version',
        'x-device-id'
    ];

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): JsonResponse
    {
        $result = [];

        foreach ($this->data as $value) {
            if ($request->headers->has($value)) {
                $result[$value] = $request->headers->get($value);
            }
        }

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
