<?php

namespace App\Http\Requests\Api\Vihara;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreViharaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'address' => [
                'required',
                'string',
            ],

            'province_id' => [
                'required',
                'integer',
                Rule::exists('provinces', 'id')
                    ->where('is_active', 1),
            ],

            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('is_active', 1),
            ],

            'district_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id')
                    ->where('is_active', 1),
            ],

            'subdistrict_id' => [
                'nullable',
                'integer',
                Rule::exists('subdistricts', 'id')
                    ->where('is_active', 1),
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
                'required_with:longitude',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
                'required_with:latitude',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],
        ];
    }
}
