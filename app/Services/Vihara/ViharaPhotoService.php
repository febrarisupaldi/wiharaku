<?php

namespace App\Services\Vihara;

use App\Repositories\MySQL\ViharaPhotoRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ViharaPhotoService
{
    public function __construct(
        protected ViharaPhotoRepository $photoRepository
    ) {}

    /**
     * Upload beberapa foto vihara.
     */
    public function upload(
        int $viharaId,
        array $photos,
        array $captions,
        ?int $uploadedBy = null
    ): array {
        if (!$this->photoRepository->existsVihara($viharaId)) {
            throw new RuntimeException('Vihara tidak ditemukan.');
        }

        $pendingStatusId = $this->photoRepository
            ->getStatusIdByCode('pending');

        if (!$pendingStatusId) {
            throw new RuntimeException(
                'Status pending foto tidak ditemukan atau tidak aktif.'
            );
        }

        $storedPaths = [];

        try {
            $sortOrder = $this->photoRepository
                ->getNextSortOrder($viharaId);

            $result = DB::transaction(function () use (
                $viharaId,
                $photos,
                $captions,
                $uploadedBy,
                $pendingStatusId,
                &$storedPaths,
                $sortOrder
            ) {
                $uploadedPhotos = [];

                foreach ($photos as $index => $photo) {
                    if (!$photo instanceof UploadedFile) {
                        throw new RuntimeException(
                            'File foto tidak valid.'
                        );
                    }

                    $path = $photo->store(
                        "vihara/photos/{$viharaId}",
                        'public'
                    );

                    if (!$path) {
                        throw new RuntimeException(
                            'Gagal menyimpan file foto.'
                        );
                    }

                    $storedPaths[] = $path;

                    $photoId = $this->photoRepository->create([
                        'vihara_id' => $viharaId,
                        'file_path' => $path,
                        'caption' => $captions[$index] ?? null,
                        'uploaded_by' => $uploadedBy,
                        'photo_review_status_id' => $pendingStatusId,
                        'is_primary' => 0,
                        'sort_order' => $sortOrder + $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $uploadedPhotos[] = [
                        'id' => $photoId,
                        'file_path' => $path,
                        'photo_url' => Storage::disk('public')
                            ->url($path),
                        'caption' => $captions[$index] ?? null,
                        'is_primary' => false,
                        'sort_order' => $sortOrder + $index,
                        'review_status' => 'pending',
                    ];
                }

                return $uploadedPhotos;
            });

            return $result;
        } catch (Throwable $e) {
            // Bersihkan file jika proses database gagal.
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }

    /**
     * Mengambil foto yang boleh ditampilkan secara publik.
     */
    public function getApprovedPhotos(int $viharaId): array
    {
        $photos = $this->photoRepository
            ->getApprovedByViharaId($viharaId);

        return $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'file_path' => $photo->file_path,
                'photo_url' => Storage::disk('public')
                    ->url($photo->file_path),
                'caption' => $photo->caption,
                'is_primary' => (bool) $photo->is_primary,
                'sort_order' => $photo->sort_order,
            ];
        })->toArray();
    }


    /**
     * Menyetujui atau menolak foto.
     */
    public function review(
        int $viharaId,
        int $photoId,
        string $status
    ): void {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new RuntimeException('Status review tidak valid.');
        }

        DB::transaction(function () use (
            $viharaId,
            $photoId,
            $status
        ) {
            $photo = $this->photoRepository
                ->findByIdAndViharaId($photoId, $viharaId, true);

            if (!$photo) {
                throw new RuntimeException('Foto tidak ditemukan.');
            }

            $statusId = $this->photoRepository
                ->getStatusIdByCode($status);

            if (!$statusId) {
                throw new RuntimeException(
                    "Status {$status} tidak ditemukan atau tidak aktif."
                );
            }

            $this->photoRepository->updateReviewStatus(
                $photoId,
                $statusId
            );

            // Jika foto utama ditolak, cari penggantinya.
            if ($status === 'rejected' && $photo->is_primary) {
                $this->photoRepository->clearPrimary($viharaId);

                $nextPhotoId = $this->photoRepository
                    ->getNextApprovedPhotoId($viharaId);

                if ($nextPhotoId) {
                    $this->photoRepository->setPrimary($nextPhotoId);
                }
            }
        });
    }

    /**
     * Menetapkan foto utama.
     */
    public function setPrimary(
        int $viharaId,
        int $photoId
    ): void {
        DB::transaction(function () use ($viharaId, $photoId) {
            $photo = $this->photoRepository
                ->findByIdAndViharaId($photoId, $viharaId, true);

            if (!$photo) {
                throw new RuntimeException('Foto tidak ditemukan.');
            }

            if ($photo->review_status !== 'approved') {
                throw new RuntimeException(
                    'Hanya foto yang sudah disetujui yang dapat menjadi foto utama.'
                );
            }

            $this->photoRepository->clearPrimary($viharaId);
            $this->photoRepository->setPrimary($photoId);
        });
    }

    /**
     * Menghapus foto.
     */
    public function delete(
        int $viharaId,
        int $photoId
    ): void {
        $filePath = DB::transaction(function () use (
            $viharaId,
            $photoId
        ) {
            $photo = $this->photoRepository
                ->findByIdAndViharaId($photoId, $viharaId, true);

            if (!$photo) {
                throw new RuntimeException('Foto tidak ditemukan.');
            }

            $this->photoRepository->softDelete($photoId);

            if ($photo->is_primary) {
                $this->photoRepository->clearPrimary($viharaId);

                $nextPhotoId = $this->photoRepository
                    ->getNextApprovedPhotoId($viharaId);

                if ($nextPhotoId) {
                    $this->photoRepository->setPrimary($nextPhotoId);
                }
            }

            return $photo->file_path;
        });

        // File dihapus setelah transaksi database berhasil.
        Storage::disk('public')->delete($filePath);
    }
}
