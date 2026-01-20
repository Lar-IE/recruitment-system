<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DigitalIdController extends Controller
{
    public function index(Request $request): View
    {
        $digitalIds = DigitalId::with(['jobseeker.user', 'employer.user', 'jobPost'])
            ->latest()
            ->paginate(10);

        return view('admin.digital-ids.index', [
            'digitalIds' => $digitalIds,
        ]);
    }

    public function revoke(Request $request, DigitalId $digitalId): RedirectResponse
    {
        $digitalId->update([
            'status' => 'inactive',
        ]);

        return redirect()->route('admin.digital-ids')
            ->with('success', __('Digital ID revoked.'));
    }

    public function show(Request $request, DigitalId $digitalId): View
    {
        return view('admin.digital-ids.show', [
            'digitalId' => $digitalId->load(['jobseeker.user', 'employer.user', 'jobPost']),
        ]);
    }
}
