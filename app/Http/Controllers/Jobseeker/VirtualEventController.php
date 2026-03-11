<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\VirtualEvent;
use App\Models\VirtualEventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VirtualEventController extends Controller
{
    public function index(Request $request): View
    {
        $jobseeker = $this->requireJobseeker($request);

        $query = VirtualEvent::with(['employer.companyProfile'])
            ->where('status', 'upcoming')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time');

        $events = $query->paginate(10)->withQueryString();

        // Get registered event IDs for current jobseeker
        $registeredEventIds = VirtualEventRegistration::where('jobseeker_id', $jobseeker->id)
            ->pluck('virtual_event_id')
            ->toArray();

        return view('jobseeker.virtual-events.index', [
            'events' => $events,
            'registeredEventIds' => $registeredEventIds,
        ]);
    }

    public function show(Request $request, VirtualEvent $virtualEvent): View
    {
        $jobseeker = $this->requireJobseeker($request);

        $virtualEvent->load(['employer.companyProfile', 'registrations']);

        $isRegistered = VirtualEventRegistration::where('virtual_event_id', $virtualEvent->id)
            ->where('jobseeker_id', $jobseeker->id)
            ->exists();

        $canRegister = $virtualEvent->canRegister($jobseeker);
        $canJoin = $isRegistered && $virtualEvent->isOngoing();
        $meetingLinkAvailable = $virtualEvent->isMeetingLinkAvailable();

        return view('jobseeker.virtual-events.show', [
            'event' => $virtualEvent,
            'isRegistered' => $isRegistered,
            'canRegister' => $canRegister,
            'canJoin' => $canJoin,
            'meetingLinkAvailable' => $meetingLinkAvailable,
        ]);
    }

    public function register(Request $request, VirtualEvent $virtualEvent): RedirectResponse
    {
        $jobseeker = $this->requireJobseeker($request);

        if (!$virtualEvent->canRegister($jobseeker)) {
            return redirect()->route('jobseeker.virtual-events.show', $virtualEvent)
                ->withErrors(['error' => __('You cannot register for this event.')]);
        }

        VirtualEventRegistration::create([
            'virtual_event_id' => $virtualEvent->id,
            'jobseeker_id' => $jobseeker->id,
            'registered_at' => now(),
        ]);

        return redirect()->route('jobseeker.virtual-events.show', $virtualEvent)
            ->with('success', __('You have successfully registered for this event.'));
    }

    private function requireJobseeker(Request $request)
    {
        $user = $request->user();
        $jobseeker = $user->jobseeker;

        if (!$jobseeker) {
            abort(403);
        }

        return $jobseeker;
    }
}
