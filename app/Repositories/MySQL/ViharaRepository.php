<?php


namespace App\Repositories\MySQL;

use App\DTOs\Vihara\CreateViharaDTO;
use App\DTOs\Vihara\UpdateViharaDTO;
use Illuminate\Support\Collection;
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
                'p.province_name',
                'c.city_name',
                'd.district_name',
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
                'p.province_name',
                'c.city_name',
                'd.district_name',
                'sd.subdistrict_name',
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

    public function search(array $filters, string $mode): Collection
    {
        $verifiedStatusId = DB::table('verification_statuses')
            ->where('code', 'verified')
            ->value('id');

        $query = DB::table('viharas as v')
            ->join('provinces as p', 'p.id', '=', 'v.province_id')
            ->join('cities as c', 'c.id', '=', 'v.city_id')
            ->leftJoin('districts as d', 'd.id', '=', 'v.district_id')
            ->leftJoin('subdistricts as s', 's.id', '=', 'v.subdistrict_id')
            ->where('v.is_active', 1)
            ->where('v.verification_status_id', $verifiedStatusId)
            ->whereNull('v.deleted_at')
            ->whereNotNull('v.latitude')
            ->whereNotNull('v.longitude')
            ->select([
                'v.id',
                'v.slug',
                'v.name',
                'v.address',
                'v.latitude',
                'v.longitude',
                'v.website',
                'p.province_name',
                'c.city_name',
                'd.district_name',
                's.subdistrict_name',
            ]);

        // Filter nama vihara
        if (!empty($filters['keyword'])) {
            $query->where('v.name', 'LIKE', '%' . $filters['keyword'] . '%');
        }

        // Filter wilayah
        foreach (
            [
                'province_id',
                'city_id',
                'district_id',
                'subdistrict_id',
            ] as $field
        ) {
            if (isset($filters[$field])) {
                $query->where('v.' . $field, $filters[$field]);
            }
        }

        // Filter tradisi Buddhis
        if (isset($filters['tradition_id'])) {
            $query->whereExists(function ($subquery) use ($filters) {
                $subquery
                    ->selectRaw('1')
                    ->from('vihara_traditions as vt')
                    ->whereColumn('vt.vihara_id', 'v.id')
                    ->where('vt.tradition_id', $filters['tradition_id']);
            });
        }

        // Filter fasilitas
        if (isset($filters['facility_id'])) {
            $query->whereExists(function ($subquery) use ($filters) {
                $subquery
                    ->selectRaw('1')
                    ->from('vihara_facilities as vf')
                    ->whereColumn('vf.vihara_id', 'v.id')
                    ->where('vf.facility_id', $filters['facility_id']);
            });
        }

        // Batas area geografis
        if ($mode === 'map') {
            $query->whereBetween('v.latitude', [
                $filters['south'],
                $filters['north'],
            ]);

            // Menangani area yang melintasi garis bujur 180 derajat
            if ($filters['west'] <= $filters['east']) {
                $query->whereBetween('v.longitude', [
                    $filters['west'],
                    $filters['east'],
                ]);
            } else {
                $query->where(function ($subquery) use ($filters) {
                    $subquery
                        ->where('v.longitude', '>=', $filters['west'])
                        ->orWhere('v.longitude', '<=', $filters['east']);
                });
            }
        }

        // Pencarian berdasarkan titik dan radius
        if (in_array($mode, ['nearby', 'radius'], true)) {
            $latitude = (float) $filters['latitude'];
            $longitude = (float) $filters['longitude'];
            $radius = (int) ($filters['radius'] ?? 5000);

            // Bounding box sebagai penyaringan awal
            $latitudeDelta = $radius / 111320;

            $cosLatitude = max(
                abs(cos(deg2rad($latitude))),
                0.000001
            );

            $longitudeDelta = min(
                180,
                $radius / (111320 * $cosLatitude)
            );

            $south = max(-90, $latitude - $latitudeDelta);
            $north = min(90, $latitude + $latitudeDelta);

            $west = $longitude - $longitudeDelta;
            $east = $longitude + $longitudeDelta;

            $query->whereBetween('v.latitude', [$south, $north]);

            if ($west < -180 || $east > 180) {
                $west = fmod($west + 540, 360) - 180;
                $east = fmod($east + 540, 360) - 180;

                $query->where(function ($subquery) use ($west, $east) {
                    $subquery
                        ->where('v.longitude', '>=', $west)
                        ->orWhere('v.longitude', '<=', $east);
                });
            } else {
                $query->whereBetween('v.longitude', [$west, $east]);
            }

            $distanceExpression = '
            ST_Distance_Sphere(
                POINT(v.longitude, v.latitude),
                POINT(?, ?)
            )
        ';

            $query->selectRaw(
                "{$distanceExpression} AS distance_m",
                [$longitude, $latitude]
            );

            $query->having('distance_m', '<=', $radius);
            $query->orderBy('distance_m');
        } else {
            $query->orderBy('v.name');
        }

        $limit = min((int) ($filters['limit'] ?? 100), 500);

        return $query
            ->limit($limit)
            ->get();
    }
}
