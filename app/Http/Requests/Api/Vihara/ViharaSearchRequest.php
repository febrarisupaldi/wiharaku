<?php

namespace App\Http\Requests\Api\Vihara;

use Illuminate\Foundation\Http\FormRequest;

class ViharaSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => [
                'required_with:longitude',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required_with:latitude',
                'numeric',
                'between:-180,180',
            ],

            'radius' => [
                'nullable',
                'integer',
                'min:100',
                'max:100000',
            ],

            'south' => ['required_with:north,west,east', 'numeric', 'between:-90,90'],
            'north' => ['required_with:south,west,east', 'numeric', 'between:-90,90'],
            'west' => ['required_with:south,north,east', 'numeric', 'between:-180,180'],
            'east' => ['required_with:south,north,west', 'numeric', 'between:-180,180'],

            'keyword' => ['nullable', 'string', 'max:100'],

            'province_id' => ['nullable', 'integer', 'min:1'],
            'city_id' => ['nullable', 'integer', 'min:1'],
            'district_id' => ['nullable', 'integer', 'min:1'],
            'subdistrict_id' => ['nullable', 'integer', 'min:1'],

            'tradition_id' => ['nullable', 'integer', 'min:1'],
            'facility_id' => ['nullable', 'integer', 'min:1'],

            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required_with' => 'Latitude dan longitude harus dikirim bersamaan.',
            'longitude.required_with' => 'Latitude dan longitude harus dikirim bersamaan.',
            'radius.max' => 'Radius pencarian maksimal 100 km.',
            'limit.max' => 'Maksimal hasil pencarian adalah 500 data.',
        ];
    }
}
