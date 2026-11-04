<?php

namespace App\DTOs\Vihara;

class CreateViharaDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $address,
        public readonly int $province_id,
        public readonly int $city_id,
        public readonly ?int $district_id,
        public readonly ?int $subdistrict_id,
        public readonly ?string $postal_code,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $description,
        public readonly ?string $website,
        public readonly ?int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            address: $data['address'],
            province_id: (int) $data['province_id'],
            city_id: (int) $data['city_id'],
            district_id: isset($data['district_id'])
                ? (int) $data['district_id'] : null,
            subdistrict_id: isset($data['subdistrict_id'])
                ? (int) $data['subdistrict_id'] : null,
            postal_code: $data['postal_code'] ?? null,
            latitude: isset($data['latitude'])
                ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude'])
                ? (float) $data['longitude'] : null,
            description: $data['description'] ?? null,
            website: $data['website'] ?? null,
            created_by: $data['created_by'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'subdistrict_id' => $this->subdistrict_id,
            'postal_code' => $this->postal_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'website' => $this->website,
            'created_by' => $this->created_by,
        ];
    }
}
