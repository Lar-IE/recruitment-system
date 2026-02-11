<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function pending(): View
    {
        return view('employer.pending');
    }

    public function jobPosts(): View
    {
        return view('employer.job-posts.index');
    }

    public function applicants(): View
    {
        return view('employer.applicants.index');
    }

    public function ats(): View
    {
        return view('employer.ats.index');
    }

    public function digitalIds(): View
    {
        return view('employer.digital-ids.index');
    }

    public function companySettings(Request $request): View
    {
        $employer = $request->attributes->get('employer');
        $isOwner = $request->attributes->get('employer_owner', false);
        $profile = $employer->companyProfile;

        return view('employer.company-settings', [
            'employer' => $employer,
            'isOwner' => $isOwner,
            'profile' => $profile,
        ]);
    }
}
