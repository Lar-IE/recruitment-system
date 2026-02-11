<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateCompanyLogoRequest;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyLogoController extends Controller
{
    public function update(UpdateCompanyLogoRequest $request): RedirectResponse
    {
        $this->assertEmployerOwner($request, __('Only the main employer can upload or update the company logo.'));
        $employer = $this->getEmployerFromRequest($request);

        // Delete old logo if exists
        $this->deleteLogoIfExists($employer);

        // Store new logo
        $logoPath = $request->file('company_logo')->store('logos', 'public');

        // Update employer record
        $employer->update([
            'company_logo' => $logoPath,
        ]);

        return redirect()->route('employer.company-settings')->with('success', __('Company logo updated successfully.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->assertEmployerOwner($request, __('Only the main employer can delete the company logo.'));
        $employer = $this->getEmployerFromRequest($request);

        // Delete logo file if exists
        $this->deleteLogoIfExists($employer);

        // Update employer record
        $employer->update([
            'company_logo' => null,
        ]);

        return redirect()->route('employer.company-settings')->with('success', __('Company logo removed successfully.'));
    }

    private function assertEmployerOwner(Request $request, string $message): void
    {
        if (! $request->attributes->get('employer_owner', false)) {
            abort(403, $message);
        }
    }

    private function getEmployerFromRequest(Request $request): Employer
    {
        /** @var Employer|null $employer */
        $employer = $request->attributes->get('employer');

        if (! $employer) {
            abort(403);
        }

        return $employer;
    }

    private function deleteLogoIfExists(Employer $employer): void
    {
        $logoPath = $employer->company_logo;

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }
    }
}
