<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\RequestDocumentUpdateRequest;
use App\Models\Document;
use App\Models\Jobseeker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $query = Jobseeker::with('user')
            ->whereHas('applications.jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            });

        if ($request->filled('status')) {
            $status = $request->string('status')->value();
            $query->whereHas('documents', function ($builder) use ($status) {
                $builder->where('status', $status);
            });
        }

        if ($request->filled('type')) {
            $type = $request->string('type')->value();
            $query->whereHas('documents', function ($builder) use ($type) {
                $builder->where('type', $type);
            });
        }

        $jobseekers = $query->withCount('documents')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employer.documents.index', [
            'jobseekers' => $jobseekers,
            'filters' => $request->only(['status', 'type']),
            'types' => $this->documentTypes(),
        ]);
    }

    public function show(Request $request, Jobseeker $jobseeker): View
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $allowed = Jobseeker::where('id', $jobseeker->id)
            ->whereHas('applications.jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->exists();

        if (! $allowed) {
            abort(403);
        }

        $documents = Document::where('jobseeker_id', $jobseeker->id)
            ->get()
            ->keyBy('type');

        return view('employer.documents.show', [
            'jobseeker' => $jobseeker->load('user'),
            'documents' => $documents,
            'types' => $this->documentTypes(),
        ]);
    }

    public function requestUpdate(RequestDocumentUpdateRequest $request, Document $document): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $allowed = Document::where('id', $document->id)
            ->whereHas('jobseeker.applications.jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->exists();

        if (! $allowed) {
            abort(403);
        }

        $companyLabel = $employer->company_name ?? $request->user()->name;
        $customReason = $request->input('remarks');
        $remarks = 'Remarks: Update request by '.$companyLabel.'. Reason: '.$customReason;

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'remarks' => $remarks,
        ]);

        return redirect()->route('employer.documents')
            ->with('success', __('Update request sent to applicant.'));
    }

    private function documentTypes(): array
    {
        return [
            'resume' => 'Resume',
            'sss' => 'SSS',
            'pagibig' => 'PAG-IBIG',
            'philhealth' => 'PhilHealth',
            'psa' => 'PSA',
        ];
    }
}
