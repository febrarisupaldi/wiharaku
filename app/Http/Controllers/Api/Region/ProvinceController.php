<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use App\Services\Region\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function __construct(
        protected RegionService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getProvinces(
            $request->only(['search'])
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
