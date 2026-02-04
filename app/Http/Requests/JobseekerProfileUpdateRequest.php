<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobseekerProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'string', 'max:4000'],
            
            // Education array validation
            'education' => ['nullable', 'array'],
            'education.*.institution' => ['required', 'string', 'max:255'],
            'education.*.degree' => ['nullable', 'string', 'max:255'],
            'education.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'education.*.start_date' => ['nullable', 'date'],
            'education.*.end_date' => ['nullable', 'date'],
            'education.*.description' => ['nullable', 'string', 'max:1000'],
            
            // Work experience array validation
            'work_experience' => ['nullable', 'array'],
            'work_experience.*.company' => ['required', 'string', 'max:255'],
            'work_experience.*.position' => ['nullable', 'string', 'max:255'],
            'work_experience.*.start_date' => ['nullable', 'date'],
            'work_experience.*.end_date' => ['nullable', 'date'],
            'work_experience.*.is_current' => ['nullable', 'boolean'],
            'work_experience.*.description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
