<?php

namespace App\Http\Requests\Api\Vihara;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViharaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:200',
            ],

            'address' => [
                'sometimes',
                'required',
                'string',
            ],

            'province_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('provinces', 'id')
                    ->where('is_active', 1),
            ],

            'city_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('is_active', 1),
            ],

            'district_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('districts', 'id')
                    ->where('is_active', 1),
            ],

            'subdistrict_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('subdistricts', 'id')
                    ->where('is_active', 1),
            ],

            'postal_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:10',
            ],

            'latitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-90,90',
                'required_with:longitude',
            ],

            'longitude' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:-180,180',
                'required_with:latitude',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'website' => [
                'sometimes',
                'nullable',
                'url',
                'max:255',
            ],
        ];
    }
}
