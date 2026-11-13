<?php

namespace App\Http\Controllers\Api\Vihara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vihara\ReviewViharaPhotoRequest;
use App\Http\Requests\Api\Vihara\UploadViharaPhotoRequest;
use App\Services\Vihara\ViharaPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ViharaPhotoController extends Controller
{
    public function __construct(
        protected ViharaPhotoService $photoService
    ) {}

    /**
     * Upload foto vihara.
     */

    public function store(
        UploadViharaPhotoRequest $request,
        int $id
    ): JsonResponse {
        try {
            $photos = $this->photoService->upload(
                viharaId: $id,
                photos: $request->file('photos', []),
                captions: $request->input('captions', []),
                uploadedBy: null,
            );

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diunggah dan menunggu review.',
                'data' => $photos,
            ], 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    /**
     * Mengambil foto yang sudah disetujui.
     */
    public function index(int $id): JsonResponse
    {
        try {
            $photos = $this->photoService
                ->getApprovedPhotos($id);

            return response()->json([
                'success' => true,
                'message' => 'Data foto berhasil diambil.',
                'data' => $photos,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data foto.',
            ], 500);
        }
    }


    /**
     * Approve atau reject foto.
     */
    public function review(
        ReviewViharaPhotoRequest $request,
        int $id,
        int $photoId
    ): JsonResponse {
        try {
            $this->photoService->review(
                viharaId: $id,
                photoId: $photoId,
                status: $request->validated('status'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Review foto berhasil diperbarui.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses review foto.',
            ], 500);
        }
    }

    /**
     * Menetapkan foto utama.
     */
    public function setPrimary(
        int $id,
        int $photoId
    ): JsonResponse {
        try {
            $this->photoService->setPrimary($id, $photoId);

            return response()->json([
                'success' => true,
                'message' => 'Foto utama berhasil diperbarui.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan foto utama.',
            ], 500);
        }
    }

    /**
     * Menghapus foto.
     */
    public function destroy(
        int $id,
        int $photoId
    ): JsonResponse {
        try {
            $this->photoService->delete($id, $photoId);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto.',
            ], 500);
        }
    }
}
