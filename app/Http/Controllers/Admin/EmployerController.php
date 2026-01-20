<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerController extends Controller
{
    public function index(): View
    {
        $employers = Employer::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.employers.index', [
            'employers' => $employers,
        ]);
    }

    public function approve(Request $request, Employer $employer): RedirectResponse
    {
        $employer->update([
            'status' => 'approved',
            'approved_at' => now(),
            'suspended_at' => null,
        ]);
        if ($employer->user) {
            $employer->user->update([
                'is_active' => true,
                'suspended_at' => null,
            ]);
        }

        return redirect()->route('admin.employers')
            ->with('success', __('Employer approved.'));
    }

    public function suspend(Request $request, Employer $employer): RedirectResponse
    {
        $employer->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
        if ($employer->user) {
            $employer->user->update([
                'is_active' => false,
                'suspended_at' => now(),
            ]);
        }

        return redirect()->route('admin.employers')
            ->with('success', __('Employer suspended.'));
    }

    public function activate(Request $request, Employer $employer): RedirectResponse
    {
        $employer->update([
            'status' => 'approved',
            'suspended_at' => null,
        ]);
        if ($employer->user) {
            $employer->user->update([
                'is_active' => true,
                'suspended_at' => null,
            ]);
        }

        return redirect()->route('admin.employers')
            ->with('success', __('Employer activated.'));
    }
}
