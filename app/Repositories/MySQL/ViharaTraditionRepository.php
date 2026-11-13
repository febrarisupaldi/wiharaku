<?php

namespace App\Repositories\MySQL;

use Illuminate\Support\Facades\DB;

class ViharaTraditionRepository
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

    public function getTraditionsByViharaId(int $viharaId): object
    {
        return DB::table('vihara_traditions', 'vt')
            ->select(
                'vt.tradition_id',
                'bt.name'
            )
            ->join('buddhist_traditions as bt', 'vt.tradition_id', '=', 'bt.id')
            ->where('vihara_id', $viharaId);
    }

    /**
     * Mengambil ID status berdasarkan code.
     */ public function create(array $data): int
    {
        return (int) DB::table('vihara_traditions')
            ->insertGetId($data);
    }
}
