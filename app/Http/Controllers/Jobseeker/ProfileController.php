<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobseekerProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;
        $jobseeker->load(['educations', 'workExperiences']);
        
        return view('jobseeker.profile.show', [
            'jobseeker' => $jobseeker,
        ]);
    }

    public function edit(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;
        $jobseeker->load(['educations', 'workExperiences']);
        
        return view('jobseeker.profile.edit', [
            'jobseeker' => $jobseeker,
        ]);
    }

    public function update(JobseekerProfileUpdateRequest $request): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;
        $validated = $request->validated();

        // Update basic profile information
        $jobseeker->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'city' => $validated['city'],
            'province' => $validated['province'] ?? null,
            'region' => $validated['region'] ?? null,
            'country' => $validated['country'] ?? null,
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'educational_attainment' => $validated['educational_attainment'],
            'bio' => $validated['bio'] ?? null,
            'skills' => $validated['skills'] ?? null,
        ]);

        // Update user's full name for consistency
        $fullName = trim(($validated['first_name'] ?? '') . ' ' . ($validated['middle_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        $request->user()->update(['name' => $fullName]);

        // Handle education entries
        if (isset($validated['education'])) {
            // Delete existing education records
            $jobseeker->educations()->delete();
            
            // Create new education records
            foreach ($validated['education'] as $index => $education) {
                if (!empty($education['institution'])) {
                    $jobseeker->educations()->create([
                        'institution' => $education['institution'],
                        'degree' => $education['degree'] ?? null,
                        'field_of_study' => $education['field_of_study'] ?? null,
                        'start_date' => $education['start_date'] ?? null,
                        'end_date' => $education['end_date'] ?? null,
                        'description' => $education['description'] ?? null,
                        'order' => $index,
                    ]);
                }
            }
        }

        // Handle work experience entries
        if (isset($validated['work_experience'])) {
            // Delete existing work experience records
            $jobseeker->workExperiences()->delete();
            
            // Create new work experience records
            foreach ($validated['work_experience'] as $index => $experience) {
                if (!empty($experience['company'])) {
                    $jobseeker->workExperiences()->create([
                        'company' => $experience['company'],
                        'position' => $experience['position'] ?? null,
                        'start_date' => $experience['start_date'] ?? null,
                        'end_date' => $experience['end_date'] ?? null,
                        'is_current' => isset($experience['is_current']) && $experience['is_current'] == '1',
                        'description' => $experience['description'] ?? null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()
            ->route('jobseeker.profile.show')
            ->with('success', __('Resume details updated.'));
    }
}
