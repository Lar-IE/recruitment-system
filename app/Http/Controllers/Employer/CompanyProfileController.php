<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $employer = $request->user()->employer;
        $profile = $employer->companyProfile;

        return view('employer.profile.edit', [
            'employer' => $employer,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $employer = $request->user()->employer;

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'industry' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:255'],
            'year_established' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        // Update or create company profile
        CompanyProfile::updateOrCreate(
            ['employer_id' => $employer->id],
            ['employer_id' => $employer->id] + $validated
        );

        return redirect()->route('employer.company-settings')
            ->with('success', __('Company profile updated successfully.'));
    }
}
