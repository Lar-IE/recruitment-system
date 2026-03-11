<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
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
