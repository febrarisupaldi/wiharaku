<?php

namespace App\Repositories\MySQL;

use Illuminate\Support\Facades\DB;

class ViharaPhotoRepository
{
    /**
     * Memastikan vihara tersedia dan belum dihapus.
     *
     * Tidak membatasi is_active agar pengelolaan foto
     * tidak bergantung pada status publikasi vihara.
     */
    public function existsVihara(int $viharaId): bool
    {
        return DB::table('viharas')
            ->where('id', $viharaId)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Mengambil ID status berdasarkan code.
     */
    public function getStatusIdByCode(string $code): ?int
    {
        $id = DB::table('photo_review_statuses')
            ->where('code', $code)
            ->where('is_active', 1)
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Mengambil urutan foto berikutnya.
     */
    public function getNextSortOrder(int $viharaId): int
    {
        $maxOrder = DB::table('vihara_photos')
            ->where('vihara_id', $viharaId)
            ->whereNull('deleted_at')
            ->max('sort_order');

        return ((int) $maxOrder) + 1;
    }

    /**
     * Menyimpan metadata foto.
     */
    public function create(array $data): int
    {
        return (int) DB::table('vihara_photos')
            ->insertGetId($data);
    }

    /**
     * Mengambil foto yang sudah disetujui untuk publik.
     */
    public function getApprovedByViharaId(int $viharaId): object
    {
        return DB::table('vihara_photos as vp')
            ->join(
                'photo_review_statuses as prs',
                'prs.id',
                '=',
                'vp.photo_review_status_id'
            )
            ->where('vp.vihara_id', $viharaId)
            ->where('prs.code', 'approved')
            ->where('prs.is_active', 1)
            ->whereNull('vp.deleted_at')
            ->select([
                'vp.id',
                'vp.file_path',
                'vp.caption',
                'vp.is_primary',
                'vp.sort_order',
            ])
            ->orderByDesc('vp.is_primary')
            ->orderBy('vp.sort_order')
            ->orderBy('vp.id')
            ->get();
    }


    /**
     * Mengambil foto berdasarkan ID dan Vihara.
     */
    public function findByIdAndViharaId(
        int $photoId,
        int $viharaId,
        bool $lock = false
    ): ?object {
        $query = DB::table('vihara_photos as vp')
            ->join(
                'photo_review_statuses as prs',
                'prs.id',
                '=',
                'vp.photo_review_status_id'
            )
            ->where('vp.id', $photoId)
            ->where('vp.vihara_id', $viharaId)
            ->whereNull('vp.deleted_at')
            ->select([
                'vp.id',
                'vp.vihara_id',
                'vp.file_path',
                'vp.is_primary',
                'vp.photo_review_status_id',
                'prs.code as review_status',
            ]);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Mengubah status review foto.
     */
    public function updateReviewStatus(
        int $photoId,
        int $statusId
    ): void {
        DB::table('vihara_photos')
            ->where('id', $photoId)
            ->update([
                'photo_review_status_id' => $statusId,
                'updated_at' => now(),
            ]);
    }

    /**
     * Menghapus penanda foto utama dari seluruh foto Vihara.
     */
    public function clearPrimary(int $viharaId): void
    {
        DB::table('vihara_photos')
            ->where('vihara_id', $viharaId)
            ->whereNull('deleted_at')
            ->update([
                'is_primary' => 0,
                'updated_at' => now(),
            ]);
    }

    /**
     * Menetapkan foto sebagai foto utama.
     */
    public function setPrimary(int $photoId): void
    {
        DB::table('vihara_photos')
            ->where('id', $photoId)
            ->update([
                'is_primary' => 1,
                'updated_at' => now(),
            ]);
    }

    /**
     * Mengambil foto approved berikutnya untuk dijadikan primary.
     */
    public function getNextApprovedPhotoId(int $viharaId): ?int
    {
        $id = DB::table('vihara_photos as vp')
            ->join(
                'photo_review_statuses as prs',
                'prs.id',
                '=',
                'vp.photo_review_status_id'
            )
            ->where('vp.vihara_id', $viharaId)
            ->where('prs.code', 'approved')
            ->where('prs.is_active', 1)
            ->whereNull('vp.deleted_at')
            ->orderBy('vp.sort_order')
            ->orderBy('vp.id')
            ->value('vp.id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Soft delete foto.
     */
    public function softDelete(int $photoId): void
    {
        DB::table('vihara_photos')
            ->where('id', $photoId)
            ->update([
                'is_primary' => 0,
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
