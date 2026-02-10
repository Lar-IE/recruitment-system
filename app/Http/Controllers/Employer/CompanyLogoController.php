<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateCompanyLogoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyLogoController extends Controller
{
    public function update(UpdateCompanyLogoRequest $request): RedirectResponse
    {
        // Only main employer can upload/update logo
        if (! $request->attributes->get('employer_owner', false)) {
            abort(403, __('Only the main employer can upload or update the company logo.'));
        }

        $employer = $request->attributes->get('employer');

        if (! $employer) {
            abort(403);
        }

        // Delete old logo if exists
        if ($employer->company_logo && Storage::disk('public')->exists($employer->company_logo)) {
            Storage::disk('public')->delete($employer->company_logo);
        }

        // Store new logo
        $logoPath = $request->file('company_logo')->store('logos', 'public');

        // Update employer record
        $employer->update([
            'company_logo' => $logoPath,
        ]);

        return redirect()->route('profile.edit')->with('success', __('Company logo updated successfully.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Only main employer can delete logo
        if (! $request->attributes->get('employer_owner', false)) {
            abort(403, __('Only the main employer can delete the company logo.'));
        }

        $employer = $request->attributes->get('employer');

        if (! $employer) {
            abort(403);
        }

        // Delete logo file if exists
        if ($employer->company_logo && Storage::disk('public')->exists($employer->company_logo)) {
            Storage::disk('public')->delete($employer->company_logo);
        }

        // Update employer record
        $employer->update([
            'company_logo' => null,
        ]);

        return redirect()->route('profile.edit')->with('success', __('Company logo removed successfully.'));
    }
}
