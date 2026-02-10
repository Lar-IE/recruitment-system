<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,svg',
                'max:2048', // 2MB in kilobytes
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'company_logo.required' => 'Please select a logo image.',
            'company_logo.image' => 'The file must be an image.',
            'company_logo.mimes' => 'The logo must be a file of type: jpg, jpeg, png, svg.',
            'company_logo.max' => 'The logo must not be larger than 2MB.',
        ];
    }
}
