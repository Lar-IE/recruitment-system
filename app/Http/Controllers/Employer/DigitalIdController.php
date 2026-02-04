<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreDigitalIdRequest;
use App\Models\Application;
use App\Models\DigitalId;
use App\Models\Jobseeker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DigitalIdController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $issuedIds = DigitalId::with(['jobseeker.user', 'jobPost'])
            ->where('employer_id', $employer->id)
            ->latest()
            ->paginate(10);

        $hiredApplications = Application::with(['jobseeker.user', 'jobPost'])
            ->whereHas('jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->where('current_status', 'hired')
            ->get();

        return view('employer.digital-ids.index', [
            'issuedIds' => $issuedIds,
            'hiredApplications' => $hiredApplications,
        ]);
    }

    public function store(StoreDigitalIdRequest $request): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $jobseeker = Jobseeker::findOrFail($request->integer('jobseeker_id'));
        $jobPostId = $request->integer('job_post_id');

        $application = Application::where('jobseeker_id', $jobseeker->id)
            ->where('job_post_id', $jobPostId)
            ->where('current_status', 'hired')
            ->whereHas('jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->firstOrFail();

        $digitalId = DigitalId::where('jobseeker_id', $jobseeker->id)
            ->where('employer_id', $employer->id)
            ->first();

        $payload = [
            'jobseeker_id' => $jobseeker->id,
            'employer_id' => $employer->id,
            'job_post_id' => $application->job_post_id,
            'file_path' => $digitalId?->file_path ?? 'digital-ids/template.svg',
            'company_name' => $request->input('company_name'),
            'job_title' => $request->input('job_title'),
            'employee_identifier' => $request->input('employee_identifier'),
            'issue_date' => $request->input('issue_date'),
            'status' => $request->input('status', 'active'),
            'issued_by' => $request->user()->id,
            'public_token' => $digitalId?->public_token ?? Str::random(48),
        ];

        if ($digitalId) {
            $digitalId->update($payload);
        } else {
            DigitalId::create($payload);
        }

        return redirect()->route('employer.digital-ids')
            ->with('success', __('Digital ID issued.'));
    }

    public function toggle(Request $request, DigitalId $digitalId): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $digitalId->employer_id !== $employer->id) {
            abort(403);
        }

        $digitalId->update([
            'status' => $digitalId->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('employer.digital-ids')
            ->with('success', __('Digital ID status updated.'));
    }

    public function show(Request $request, DigitalId $digitalId): View
    {
        $employer = $request->user()->employer;

        if (! $employer || $digitalId->employer_id !== $employer->id) {
            abort(403);
        }

        if (! $digitalId->public_token) {
            $digitalId->update([
                'public_token' => Str::random(48),
            ]);
        }

        return view('employer.digital-ids.show', [
            'digitalId' => $digitalId->load(['jobseeker.user', 'jobPost']),
        ]);
    }
}
