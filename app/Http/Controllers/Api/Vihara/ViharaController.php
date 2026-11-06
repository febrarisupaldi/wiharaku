<?php

namespace App\Http\Controllers\Api\Vihara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vihara\StoreViharaRequest;
use App\Http\Requests\Api\Vihara\UpdateViharaRequest;
use App\DTOs\Vihara\CreateViharaDTO;
use App\DTOs\Vihara\UpdateViharaDTO;
use App\Services\Vihara\ViharaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViharaController extends Controller
{
    public function __construct(
        protected ViharaService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll(
            $request->only([
                'search',
                'province_id',
                'city_id',
                'is_active',
                'verification_status_id',
            ])
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(StoreViharaRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['created_by'] = auth()->id();

        $dto = CreateViharaDTO::fromArray($data);

        $id = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Vihara berhasil dibuat.',
            'data' => [
                'id' => $id,
            ],
        ], 201);
    }

    public function update(
        UpdateViharaRequest $request,
        int $id
    ): JsonResponse {
        $data = $request->validated();

        $data['updated_by'] = auth()->id();

        $dto = UpdateViharaDTO::fromArray($data);

        $this->service->update($id, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Vihara berhasil diperbarui.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Vihara berhasil dihapus.',
        ]);
    }

    public function publish(int $id): JsonResponse
    {
        $this->service->publish($id);

        return response()->json([
            'success' => true,
            'message' => 'Vihara berhasil dipublikasikan.',
        ]);
    }

    public function unpublish(int $id): JsonResponse
    {
        $this->service->unpublish($id);

        return response()->json([
            'success' => true,
            'message' => 'Vihara berhasil dinonaktifkan.',
        ]);
    }
}
