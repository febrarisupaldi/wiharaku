<?php

namespace App\Repositories\MySQL;

use Illuminate\Support\Facades\DB;

class RegionRepository
{
    public function getProvinces(array $filters = [])
    {
        return DB::table('provinces')
            ->select('id', 'province_code', 'province_name')
            ->where('is_active', 1)
            ->when(
                $filters['search'] ?? null,
                fn($query, $search) =>
                $query->where('province_name', 'like', "%{$search}%")
            )
            ->orderBy('province_name')
            ->get();
    }

    public function getCities(array $filters = [])
    {
        return DB::table('cities as c')
            ->join('provinces as p', 'p.id', '=', 'c.province_id')
            ->select([
                'c.id',
                'c.city_code',
                'c.city_name',
                'c.city_type',
                'c.province_id',
                'p.province_name',
            ])
            ->where('c.is_active', 1)
            ->where('p.is_active', 1)
            ->when(
                $filters['province_id'] ?? null,
                function ($query) use ($filters) {
                    return $query->where('c.province_id', $filters['province_id']);
                }
            )
            ->when(
                $filters['search'] ?? null,
                fn($query, $search) =>
                $query->where('c.name', 'like', "%{$search}%")
            )
            ->orderBy('c.city_name')
            ->get();
    }

    public function getDistricts(array $filters = [])
    {
        return DB::table('districts as d')
            ->join('cities as c', 'c.id', '=', 'd.city_id')
            ->select([
                'd.id',
                'd.district_code',
                'd.district_name',
                'd.city_id',
                'c.city_name',
            ])
            ->where('d.is_active', 1)
            ->where('c.is_active', 1)
            ->when(
                $filters['city_id'] ?? null,
                fn($query, $id) =>
                $query->where('d.city_id', $id)
            )
            ->when(
                $filters['search'] ?? null,
                fn($query, $search) =>
                $query->where('d.district_name', 'like', "%{$search}%")
            )
            ->orderBy('d.district_name')
            ->get();
    }

    public function getSubdistricts(array $filters = [])
    {
        return DB::table('subdistricts as s')
            ->join('districts as d', 'd.id', '=', 's.district_id')
            ->select([
                's.id',
                's.subdistrict_code',
                's.subdistrict_name',
                's.district_id',
                's.postal_code',
                'd.district_name',
            ])
            ->where('s.is_active', 1)
            ->where('d.is_active', 1)
            ->when(
                $filters['district_id'] ?? null,
                fn($query, $id) =>
                $query->where('s.district_id', $id)
            )
            ->when(
                $filters['search'] ?? null,
                fn($query, $search) =>
                $query->where('s.subdistrict_name', 'like', "%{$search}%")
            )
            ->orderBy('s.subdistrict_name')
            ->get();
    }

    public function findProvinceById(int $id)
    {
        return DB::table('provinces')
            ->where('id', $id)
            ->first();
    }

    public function findCityById(int $id)
    {
        return DB::table('cities')
            ->where('id', $id)
            ->first();
    }

    public function findDistrictById(int $id)
    {
        return DB::table('districts')
            ->where('id', $id)
            ->first();
    }

    public function findSubdistrictById(int $id)
    {
        return DB::table('subdistricts')
            ->where('id', $id)
            ->first();
    }
}
