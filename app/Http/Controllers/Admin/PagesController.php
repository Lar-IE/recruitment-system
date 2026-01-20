<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function users(): View
    {
        return view('admin.users.index');
    }

    public function employers(): View
    {
        return view('admin.employers.index');
    }

    public function jobseekers(): View
    {
        return view('admin.jobseekers.index');
    }

    public function jobPosts(): View
    {
        return view('admin.job-posts.index');
    }

    public function applications(): View
    {
        return view('admin.applications.index');
    }

    public function documents(): View
    {
        return view('admin.documents.index');
    }

    public function digitalIds(): View
    {
        return view('admin.digital-ids.index');
    }

    public function reports(): View
    {
        return view('admin.reports.index');
    }

    public function settings(): View
    {
        return view('admin.settings.index');
    }
}
