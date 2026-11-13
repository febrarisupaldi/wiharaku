<?php

namespace App\Http\Requests\Api\Vihara;

use Illuminate\Foundation\Http\FormRequest;

class UploadViharaPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'captions' => [
                'sometimes',
                'array',
            ],
            'captions.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Minimal satu foto harus diunggah.',
            'photos.max' => 'Maksimal 10 foto dalam satu kali upload.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ];
    }
}
