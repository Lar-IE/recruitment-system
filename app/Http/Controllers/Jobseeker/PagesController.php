<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function jobs(): View
    {
        return view('jobseeker.jobs.index');
    }

    public function documents(): View
    {
        return view('jobseeker.documents.index');
    }

    public function digitalId(): View
    {
        $digitalId = null;
        $jobseeker = auth()->user()->jobseeker;

        if ($jobseeker) {
            $digitalId = $jobseeker->digitalIds()
                ->latest()
                ->first();
        }

        if ($digitalId && ! $digitalId->public_token) {
            $digitalId->update([
                'public_token' => Str::random(48),
            ]);
        }

        return view('jobseeker.digital-id.index', [
            'digitalId' => $digitalId,
        ]);
    }

    public function history(): View
    {
        return view('jobseeker.history.index');
    }
}
