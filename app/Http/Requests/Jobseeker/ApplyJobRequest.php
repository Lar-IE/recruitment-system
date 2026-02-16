<?php

namespace App\Http\Requests\Jobseeker;

use Illuminate\Foundation\Http\FormRequest;

class ApplyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'cover_letter_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover_letter_file.mimes' => __('The cover letter must be a PDF or Word file (.pdf, .doc, .docx).'),
            'cover_letter_file.max' => __('The cover letter file must not exceed 5 MB.'),
        ];
    }
}
