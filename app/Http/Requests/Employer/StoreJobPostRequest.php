<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'job_type' => ['required', 'in:full_time,part_time,contract,temporary,internship'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'salary_type' => ['required', 'in:daily_rate,fixed,salary_range'],
            'salary_daily' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'required_if:salary_type,daily_rate'],
            'salary_monthly' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'required_if:salary_type,fixed'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'required_if:salary_type,salary_range'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'gte:salary_min', 'required_if:salary_type,salary_range'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', 'in:draft,published,closed'],
            'application_deadline' => ['nullable', 'date'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*.skill_name' => ['nullable', 'string', 'max:255'],
            'required_skills.*.weight' => ['nullable', 'integer', 'min:1', 'max:3'],
            'required_skills.*.min_proficiency' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
