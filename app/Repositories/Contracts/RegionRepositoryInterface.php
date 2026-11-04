<?php

namespace App\Repositories\Contracts;

interface RegionRepositoryInterface
{
    public function getProvinces(array $filters = []);

    public function getCities(array $filters = []);

    public function getDistricts(array $filters = []);

    public function getSubdistricts(array $filters = []);

    public function findProvinceById(int $id);

    public function findCityById(int $id);

    public function findDistrictById(int $id);

    public function findSubdistrictById(int $id);
}
