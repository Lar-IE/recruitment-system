<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagesController extends Controller
{
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
