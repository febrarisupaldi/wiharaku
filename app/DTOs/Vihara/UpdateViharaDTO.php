<?php

namespace App\DTOs\Vihara;

class UpdateViharaDTO
{
    public function __construct(
        public readonly array $attributes,
        public readonly ?int $updated_by,
    ) {}

    public static function fromArray(array $data): self
    {
        $fields = [
            'name',
            'address',
            'province_id',
            'city_id',
            'district_id',
            'subdistrict_id',
            'postal_code',
            'latitude',
            'longitude',
            'description',
            'website',
        ];

        $attributes = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        return new self(
            attributes: $attributes,
            updated_by: isset($data['updated_by'])
                ? (int) $data['updated_by'] : null,
        );
    }

    public function toArray(): array
    {
        $data = $this->attributes;

        if ($this->updated_by !== null) {
            $data['updated_by'] = $this->updated_by;
        }

        return $data;
    }
}
