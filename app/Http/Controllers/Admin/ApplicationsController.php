<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class ApplicationsController extends Controller
{
    public function index(): View
    {
        $applications = Application::with([
            'jobPost',
            'jobseeker.user',
            'notes.creator',
            'notes.employer',
        ])->latest('applied_at')->paginate(10);

        return view('admin.applications.index', [
            'applications' => $applications,
        ]);
    }
}
