<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobseeker\UpdateDigitalIdPhotoRequest;
use App\Models\DigitalId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DigitalIdController extends Controller
{
    public function updatePhoto(UpdateDigitalIdPhotoRequest $request): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            abort(403);
        }

        $digitalId = DigitalId::where('jobseeker_id', $jobseeker->id)->latest()->first();

        if (! $digitalId) {
            return redirect()->route('jobseeker.digital-id')
                ->withErrors(['photo' => __('No digital ID found.')]);
        }

        $file = $request->file('photo');
        $path = $file->store("digital-ids/{$jobseeker->id}/photos", 'public');

        if ($digitalId->photo_path && Storage::disk('public')->exists($digitalId->photo_path)) {
            Storage::disk('public')->delete($digitalId->photo_path);
        }

        $digitalId->update([
            'photo_path' => $path,
        ]);

        return redirect()->route('jobseeker.digital-id')
            ->with('success', __('Photo uploaded.'));
    }
}
