<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PagesController extends Controller
{
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
}
