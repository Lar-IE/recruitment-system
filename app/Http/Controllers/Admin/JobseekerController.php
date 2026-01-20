<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jobseeker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobseekerController extends Controller
{
    public function index(): View
    {
        $jobseekers = Jobseeker::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.jobseekers.index', [
            'jobseekers' => $jobseekers,
        ]);
    }

    public function suspend(Request $request, Jobseeker $jobseeker): RedirectResponse
    {
        $jobseeker->update([
            'status' => 'suspended',
        ]);

        return redirect()->route('admin.jobseekers')
            ->with('success', __('Jobseeker suspended.'));
    }

    public function activate(Request $request, Jobseeker $jobseeker): RedirectResponse
    {
        $jobseeker->update([
            'status' => 'active',
        ]);

        return redirect()->route('admin.jobseekers')
            ->with('success', __('Jobseeker activated.'));
    }
}
