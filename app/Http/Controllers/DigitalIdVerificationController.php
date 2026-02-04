<?php

namespace App\Http\Controllers;

use App\Models\DigitalId;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        return view('digital-ids.verify', [
            'digitalId' => $digitalId,
            'documents' => $documents,
        ]);
    }

    public function download(Request $request, string $token, string $type)
    {
        $digitalId = DigitalId::where('public_token', $token)->firstOrFail();

        if (!in_array($type, ['sss', 'pagibig', 'philhealth', 'psa'], true)) {
            abort(404);
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if ((string) $request->string('password') !== (string) $digitalId->employee_identifier) {
            return back()->withErrors([
                'password' => __('Incorrect password.'),
            ]);
        }

        $document = Document::where('jobseeker_id', $digitalId->jobseeker_id)
            ->where('type', $type)
            ->firstOrFail();

        $path = Storage::disk('public')->path($document->file_path);

        return response()->download($path, $type.'-document');
    }
}
