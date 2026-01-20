<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewDocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with(['jobseeker.user', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->value());
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->value());
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('admin.documents.index', [
            'documents' => $documents,
            'filters' => $request->only(['status', 'type']),
            'types' => $this->documentTypes(),
        ]);
    }

    public function approve(ReviewDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('admin.documents')
            ->with('success', __('Document approved.'));
    }

    public function reject(ReviewDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('admin.documents')
            ->with('success', __('Document rejected.'));
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
