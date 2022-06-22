<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    /**
    * @var CountryService $service;
    */
    private CountryService $service;

    public function __construct(CountryService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $countries = $this->service->repository->get();

        return response()->json(CountryResource::collection($countries));
    }

    public function show(int $id): JsonResponse
    {
        $foundCountry = $this->service->repository->find($id);

        return response()->json(CountryResource::make($foundCountry));
    }
}
