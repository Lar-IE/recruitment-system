<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class StoreDigitalIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jobseeker_id' => ['required', 'integer', 'exists:jobseekers,id'],
            'job_post_id' => ['required', 'integer', 'exists:job_posts,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'employee_identifier' => ['required', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
