<?php

namespace App\Services\Vihara;

use App\DTOs\Vihara\CreateViharaDTO;
use App\DTOs\Vihara\UpdateViharaDTO;
use App\Repositories\MySQL\RegionRepository;
use App\Repositories\MySQL\ViharaRepository;
use App\Services\Support\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ViharaService
{
    public function __construct(
        protected ViharaRepository $repository,
        protected RegionRepository $regionRepository,
        protected AuditLogService $auditLogService
    ) {
        $auditLogService = $this->auditLogService;
    }

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function find(int $id)
    {
        $vihara = $this->repository->findById($id);

        if (!$vihara) {
            throw ValidationException::withMessages([
                'vihara' => 'Data vihara tidak ditemukan.',
            ]);
        }

        return $vihara;
    }

    public function create(CreateViharaDTO $dto): int
    {
        return DB::transaction(function () use ($dto) {
            $this->validateRegion($dto->toArray());

            $this->validateCoordinates($dto->toArray());

            return $this->repository->create($dto);
        });
    }

    public function update(
        int $id,
        UpdateViharaDTO $dto
    ): bool {
        return DB::transaction(function () use ($id, $dto) {
            $vihara = $this->find($id);

            $attributes = $dto->attributes;

            $this->validateRegion(
                array_merge(
                    [
                        'province_id' => $vihara->province_id,
                        'city_id' => $vihara->city_id,
                        'district_id' => $vihara->district_id,
                        'subdistrict_id' => $vihara->subdistrict_id,
                    ],
                    $attributes
                )
            );

            $this->validateCoordinates(
                array_merge(
                    [
                        'latitude' => $vihara->latitude,
                        'longitude' => $vihara->longitude,
                    ],
                    $attributes
                )
            );



            // Jika nama tidak berubah, slug tidak perlu diubah.
            // Updated_by hanya diubah jika actor tersedia.

            $check = $this->repository->update($id, $dto);

            $newVihara = $this->find($id);

            $this->auditLogService->record('vihara', $id, 'UPDATE', (array) $vihara, (array) $newVihara);

            return $check;
        });
    }

    public function delete(int $id): bool
    {
        $this->find($id);

        return DB::transaction(
            fn() => $this->repository->delete($id)
        );
    }

    public function publish(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $vihara = $this->find($id);

            if (
                $vihara->latitude === null ||
                $vihara->longitude === null
            ) {
                throw ValidationException::withMessages([
                    'coordinates' => 'Koordinat wajib diisi.',
                ]);
            }

            if (
                $vihara->verification_status !== 'verified'
            ) {
                throw ValidationException::withMessages([
                    'verification_status' =>
                    'Vihara harus diverifikasi sebelum dipublikasikan.',
                ]);
            }

            return $this->repository->updatePublication(
                $id,
                true
            );
        });
    }

    public function unpublish(int $id): bool
    {
        $this->find($id);

        return $this->repository->updatePublication(
            $id,
            false
        );
    }

    private function validateRegion(array $data): void
    {
        $city = DB::table('cities')
            ->where('id', $data['city_id'])
            ->where('province_id', $data['province_id'])
            ->where('is_active', 1)
            ->exists();

        if (!$city) {
            throw ValidationException::withMessages([
                'city_id' =>
                'Kabupaten/kota tidak sesuai dengan provinsi.',
            ]);
        }

        if (!empty($data['district_id'])) {
            $district = DB::table('districts')
                ->where('id', $data['district_id'])
                ->where('city_id', $data['city_id'])
                ->where('is_active', 1)
                ->exists();

            if (!$district) {
                throw ValidationException::withMessages([
                    'district_id' =>
                    'Kecamatan tidak sesuai dengan kabupaten/kota.',
                ]);
            }
        }

        if (!empty($data['subdistrict_id'])) {
            if (empty($data['district_id'])) {
                throw ValidationException::withMessages([
                    'subdistrict_id' =>
                    'Kecamatan harus dipilih terlebih dahulu.',
                ]);
            }

            $subdistrict = DB::table('subdistricts')
                ->where('id', $data['subdistrict_id'])
                ->where('district_id', $data['district_id'])
                ->where('is_active', 1)
                ->exists();

            if (!$subdistrict) {
                throw ValidationException::withMessages([
                    'subdistrict_id' =>
                    'Kelurahan/desa tidak sesuai dengan kecamatan.',
                ]);
            }
        }
    }

    private function validateCoordinates(array $data): void
    {
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;

        if (
            ($latitude === null && $longitude !== null) ||
            ($latitude !== null && $longitude === null)
        ) {
            throw ValidationException::withMessages([
                'coordinates' =>
                'Latitude dan longitude harus diisi bersamaan.',
            ]);
        }
    }
}
