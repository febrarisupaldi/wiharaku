<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Region\RegionService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(
        protected RegionService $service
    ) {}

    public function index(Request $request)
    {
        $data = $this->service->getCities(
            $request->only(['search'])
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
