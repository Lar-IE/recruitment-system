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
        $jobseeker->load(['educations', 'workExperiences', 'skillsList']);
        
        return view('jobseeker.profile.show', [
            'jobseeker' => $jobseeker,
        ]);
    }

    public function edit(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;
        $jobseeker->load(['educations', 'workExperiences', 'skillsList']);
        
        return view('jobseeker.profile.edit', [
            'jobseeker' => $jobseeker,
        ]);
    }

    public function update(JobseekerProfileUpdateRequest $request): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;
        $validated = $request->validated();

        // Work Experience #1 is always treated as current/recent when filled (no checkbox)
        $workExperienceEntries = $validated['work_experience'] ?? [];
        $firstWeFilled = ! empty(($workExperienceEntries[0] ?? [])['company'] ?? '');
        $workExperience1CurrentOrRecent = $firstWeFilled;

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
            'work_experience_1_current_or_recent' => $workExperience1CurrentOrRecent,
        ]);

        // Handle skills with proficiency
        if (isset($validated['skills'])) {
            $jobseeker->skillsList()->delete();
            foreach ($validated['skills'] as $index => $skill) {
                if (! empty(trim($skill['skill_name'] ?? ''))) {
                    $jobseeker->skillsList()->create([
                        'skill_name' => trim($skill['skill_name']),
                        'proficiency_percentage' => (int) ($skill['proficiency_percentage'] ?? 50),
                        'order' => $index,
                    ]);
                }
            }
        }

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

            $workExperienceEntries = $validated['work_experience'];
            $validWorkExperienceCount = count(array_filter($workExperienceEntries, fn ($e) => ! empty($e['company'] ?? '')));

            foreach ($workExperienceEntries as $index => $experience) {
                if (! empty($experience['company'])) {
                    // WE#1 is always stored as current when filled; WE#2+ are not.
                    $isCurrent = $index === 0;

                    $jobseeker->workExperiences()->create([
                        'company' => trim($experience['company'] ?? ''),
                        'position' => trim($experience['position'] ?? '') ?: 'N/A',
                        'start_date' => $experience['start_date'] ?? null,
                        'end_date' => $experience['end_date'] ?? null,
                        'is_current' => $isCurrent,
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
