<?php


namespace App\Repositories\MySQL;

use App\DTOs\Vihara\CreateViharaDTO;
use App\DTOs\Vihara\UpdateViharaDTO;
use Illuminate\Support\Facades\DB;

class ViharaRepository
{
    public function getAll(array $filters = [])
    {
        return DB::table('viharas as v')
            ->join('provinces as p', 'p.id', '=', 'v.province_id')
            ->join('cities as c', 'c.id', '=', 'v.city_id')
            ->join(
                'verification_statuses as vs',
                'vs.id',
                '=',
                'v.verification_status_id'
            )
            ->leftJoin('districts as d', 'd.id', '=', 'v.district_id')
            ->select([
                'v.id',
                'v.slug',
                'v.name',
                'v.address',
                'v.latitude',
                'v.longitude',
                'v.is_active',
                'v.verification_status_id',
                'vs.code as verification_status',
                'p.name as province_name',
                'c.name as city_name',
                'd.name as district_name',
                'v.created_at',
            ])
            ->whereNull('v.deleted_at')
            ->when(
                isset($filters['is_active']),
                fn($query) => $query->where(
                    'v.is_active',
                    $filters['is_active']
                )
            )
            ->when(
                isset($filters['province_id']),
                fn($query) => $query->where(
                    'v.province_id',
                    $filters['province_id']
                )
            )
            ->when(
                isset($filters['city_id']),
                fn($query) => $query->where(
                    'v.city_id',
                    $filters['city_id']
                )
            )
            ->when(
                isset($filters['verification_status_id']),
                fn($query) => $query->where(
                    'v.verification_status_id',
                    $filters['verification_status_id']
                )
            )
            ->when(
                !empty($filters['search']),
                fn($query) => $query->where(
                    'v.name',
                    'like',
                    '%' . $filters['search'] . '%'
                )
            )
            ->orderBy('v.name')
            ->get();
    }

    public function findById(int $id)
    {
        return DB::table('viharas as v')
            ->join('provinces as p', 'p.id', '=', 'v.province_id')
            ->join('cities as c', 'c.id', '=', 'v.city_id')
            ->join(
                'verification_statuses as vs',
                'vs.id',
                '=',
                'v.verification_status_id'
            )
            ->leftJoin('districts as d', 'd.id', '=', 'v.district_id')
            ->leftJoin(
                'subdistricts as sd',
                'sd.id',
                '=',
                'v.subdistrict_id'
            )
            ->select([
                'v.*',
                'p.name as province_name',
                'c.name as city_name',
                'd.name as district_name',
                'sd.name as subdistrict_name',
                'vs.code as verification_status',
            ])
            ->where('v.id', $id)
            ->whereNull('v.deleted_at')
            ->first();
    }

    public function findBySlug(string $slug)
    {
        return DB::table('viharas')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();
    }

    public function create(CreateViharaDTO $dto): int
    {
        return (int) DB::table('viharas')->insertGetId([
            'slug' => $this->generateSlug($dto->name),
            ...$dto->toArray(),
            'verification_status_id' => DB::table(
                'verification_statuses'
            )->where('code', 'unverified')->value('id'),
            'is_active' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(
        int $id,
        UpdateViharaDTO $dto
    ): bool {
        $attributes = $dto->toArray();

        if (array_key_exists('name', $attributes)) {
            $attributes['slug'] = $this->generateSlug(
                $attributes['name'],
                $id
            );
        }

        $attributes['updated_at'] = now();

        return DB::table('viharas')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($attributes) > 0;
    }

    public function delete(int $id): bool
    {
        return DB::table('viharas')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'is_active' => 0,
                'updated_at' => now(),
            ]) > 0;
    }

    public function updatePublication(
        int $id,
        bool $isActive
    ): bool {
        return DB::table('viharas')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'is_active' => $isActive,
                'updated_at' => now(),
            ]) > 0;
    }

    private function generateSlug(
        string $name,
        ?int $excludeId = null
    ): string {
        $base = \Illuminate\Support\Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (
            DB::table('viharas')
            ->where('slug', $slug)
            ->when(
                $excludeId,
                fn($query) => $query->where('id', '<>', $excludeId)
            )
            ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
