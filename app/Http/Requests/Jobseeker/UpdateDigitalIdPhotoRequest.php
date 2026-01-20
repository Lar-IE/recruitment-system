<?php

namespace App\Http\Requests\Jobseeker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDigitalIdPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'file', 'max:4096', 'mimes:jpg,jpeg,png'],
        ];
    }
}
