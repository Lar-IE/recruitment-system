<?php

namespace App\Http\Controllers;

use App\Models\DigitalId;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class DigitalIdVerificationController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $digitalId = DigitalId::with(['jobseeker.user', 'employer.user', 'jobPost'])
            ->where('public_token', $token)
            ->firstOrFail();

        $documents = Document::where('jobseeker_id', $digitalId->jobseeker_id)
            ->whereIn('type', ['sss', 'pagibig', 'philhealth', 'psa'])
            ->get()
            ->keyBy('type');
        $downloadUrls = [];

        foreach (['sss', 'pagibig', 'philhealth', 'psa'] as $type) {
            if (isset($documents[$type])) {
                $downloadUrls[$type] = URL::temporarySignedRoute(
                    'digital-ids.verify.documents.download',
                    now()->addMinutes(10),
                    [
                        'token' => $digitalId->public_token,
                        'type' => $type,
                    ]
                );
            }
        }

        return view('digital-ids.verify', [
            'digitalId' => $digitalId,
            'documents' => $documents,
            'downloadUrls' => $downloadUrls,
        ]);
    }

    public function download(Request $request, string $token, string $type)
    {
        $digitalId = DigitalId::where('public_token', $token)->firstOrFail();

        if (!in_array($type, ['sss', 'pagibig', 'philhealth', 'psa'], true)) {
            abort(404);
        }

        $document = Document::where('jobseeker_id', $digitalId->jobseeker_id)
            ->where('type', $type)
            ->firstOrFail();

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $type.'-document');
    }
}
