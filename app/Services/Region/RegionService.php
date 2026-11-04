<?php

namespace App\Services\Region;

use App\Repositories\MySQL\RegionRepository;

class RegionService
{
    public function __construct(
        protected RegionRepository $repository
    ) {}

    public function getProvinces(array $filters = [])
    {
        return $this->repository->getProvinces($filters);
    }

    public function getCities(array $filters = [])
    {
        return $this->repository->getCities($filters);
    }

    public function getDistricts(array $filters = [])
    {
        return $this->repository->getDistricts($filters);
    }

    public function getSubdistricts(array $filters = [])
    {
        return $this->repository->getSubdistricts($filters);
    }
}
