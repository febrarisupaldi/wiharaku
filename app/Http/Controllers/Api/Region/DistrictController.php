<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use App\Services\Region\RegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function __construct(
        protected RegionService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getDistricts(
            $request->only(['search'])
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
