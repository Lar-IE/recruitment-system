<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(Employer $employer): View
    {
        $employer->load([
            'companyProfile',
            'jobPosts' => function ($query) {
                $query->where('status', 'published')
                    ->where('deadline', '>=', now())
                    ->latest();
            },
        ]);

        return view('company.show', [
            'employer' => $employer,
            'profile' => $employer->companyProfile,
            'jobPosts' => $employer->jobPosts,
        ]);
    }
}
