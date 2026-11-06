<?php

namespace App\Http\Controllers\Api\Vihara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vihara\ViharaSearchRequest;
use App\Services\Vihara\ViharaSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViharaSearchController extends Controller
{
    public function __construct(
        private readonly ViharaSearchService $service
    ) {}

    public function nearby(ViharaSearchRequest $request): JsonResponse
    {
        $data = $this->service->nearby($request->validated());
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function radius(ViharaSearchRequest $request): JsonResponse
    {
        $data = $this->service->radius($request->validated());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function map(ViharaSearchRequest $request): JsonResponse
    {
        $data = $this->service->map($request->validated());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
