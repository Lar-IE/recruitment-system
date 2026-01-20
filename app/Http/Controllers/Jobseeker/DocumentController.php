<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobseeker\StoreDocumentRequest;
use App\Http\Requests\Jobseeker\StoreDocumentsBatchRequest;
use App\Models\Document;
use App\Models\Jobseeker;
use App\Notifications\DocumentUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;
        $documents = collect();

        if ($jobseeker) {
            $documents = Document::where('jobseeker_id', $jobseeker->id)
                ->get()
                ->keyBy('type');
        }

        return view('jobseeker.documents.index', [
            'documents' => $documents,
            'types' => $this->documentTypes(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            abort(403);
        }

        $type = $request->string('type')->value();
        $file = $request->file('file');
        $this->saveDocument($jobseeker->id, $type, $file);

        return redirect()->route('jobseeker.documents')
            ->with('success', __('Document uploaded and pending review.'));
    }

    public function storeBatch(StoreDocumentsBatchRequest $request): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            abort(403);
        }

        $files = $request->file('files', []);

        DB::transaction(function () use ($jobseeker, $files) {
            foreach ($files as $type => $file) {
                $this->saveDocument($jobseeker->id, $type, $file);
            }
        });

        return redirect()->route('jobseeker.documents')
            ->with('success', __('All documents uploaded and pending review.'));
    }

    public function download(Request $request, Document $document)
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker || $document->jobseeker_id !== $jobseeker->id) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path);
    }

    public function show(Request $request, Document $document): View
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker || $document->jobseeker_id !== $jobseeker->id) {
            abort(403);
        }

        return view('jobseeker.documents.show', [
            'document' => $document,
            'typeLabel' => $this->documentTypes()[$document->type] ?? strtoupper($document->type),
        ]);
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

    private function saveDocument(int $jobseekerId, string $type, $file): void
    {
        $document = Document::where('jobseeker_id', $jobseekerId)
            ->where('type', $type)
            ->first();

        $previousStatus = $document?->status;
        $path = $file->store("documents/{$jobseekerId}", 'public');

        if ($document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->update([
                'file_path' => $path,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'remarks' => null,
            ]);
        } else {
            Document::create([
                'jobseeker_id' => $jobseekerId,
                'type' => $type,
                'file_path' => $path,
                'status' => 'pending',
            ]);
        }

        if ($previousStatus === 'rejected') {
            $this->notifyEmployersDocumentUpdated($jobseekerId, $type);
        }
    }

    private function notifyEmployersDocumentUpdated(int $jobseekerId, string $type): void
    {
        $jobseeker = Jobseeker::with(['user', 'applications.jobPost.employer.user'])
            ->find($jobseekerId);

        if (! $jobseeker || ! $jobseeker->user) {
            return;
        }

        $employerUsers = $jobseeker->applications
            ->map(fn ($application) => $application->jobPost?->employer?->user)
            ->filter()
            ->unique('id');

        foreach ($employerUsers as $employerUser) {
            $employerUser->notify(new DocumentUpdated(
                $jobseeker->id,
                $jobseeker->user->name,
                $type
            ));
        }
    }
}
