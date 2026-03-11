<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicJobController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $jobPost = JobPost::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['employer.companyProfile', 'requiredSkills'])
            ->firstOrFail();

        return view('jobs.public-show', [
            'jobPost' => $jobPost,
        ]);
    }
}

