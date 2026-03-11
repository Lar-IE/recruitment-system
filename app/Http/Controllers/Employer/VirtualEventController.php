<?php

namespace App\Http\Controllers\Employer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\VirtualEvent;
use App\Notifications\VirtualEventCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VirtualEventController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $this->requireEmployer($request);

        $statusFilter = $request->string('status')->value();
        $validStatuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];
        if ($statusFilter && in_array($statusFilter, $validStatuses, true)) {
            $query = VirtualEvent::where('employer_id', $employer->id)->where('status', $statusFilter);
        } else {
            $query = VirtualEvent::where('employer_id', $employer->id);
            $statusFilter = null;
        }

        $events = $query->withCount('registrations')->latest('date')->paginate(10)->withQueryString();

        return view('employer.virtual-events.index', [
            'events' => $events,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function create(): View
    {
        return view('employer.virtual-events.create', [
            'platforms' => $this->platforms(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $employer = $this->requireEmployer($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'platform' => ['required', 'string', 'max:255'],
            'meeting_link' => ['required', 'url'],
            'registration_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'status' => ['nullable', 'in:upcoming,ongoing,completed,cancelled'],
        ], [
            'end_time.after' => __('End time must be after start time.'),
        ]);

        // Custom validation: registration_deadline must be before or on event date
        if (!empty($validated['registration_deadline']) && !empty($validated['date'])) {
            $deadline = \Carbon\Carbon::parse($validated['registration_deadline']);
            $eventDate = \Carbon\Carbon::parse($validated['date']);
            if ($deadline->isAfter($eventDate)) {
                return redirect()->back()
                    ->withErrors(['registration_deadline' => __('Registration deadline must be before or on the event date.')])
                    ->withInput();
            }
        }

        $validated['employer_id'] = $employer->id;
        $validated['status'] = $validated['status'] ?? 'upcoming';

        $event = VirtualEvent::create($validated);

        // Notify all jobseekers
        try {
            $this->notifyAllJobseekers($event);
        } catch (\Exception $e) {
            Log::error('Failed to notify jobseekers about virtual event: ' . $e->getMessage());
        }

        return redirect()->route('employer.virtual-events.show', $event)
            ->with('success', __('Virtual event created successfully. All jobseekers have been notified.'));
    }

    public function show(Request $request, VirtualEvent $virtualEvent): View
    {
        $virtualEvent = $this->findEmployerVirtualEvent($request, $virtualEvent->id);
        $virtualEvent->load(['registrations.jobseeker.user']);

        return view('employer.virtual-events.show', [
            'event' => $virtualEvent,
        ]);
    }

    public function edit(Request $request, VirtualEvent $virtualEvent): View
    {
        $virtualEvent = $this->findEmployerVirtualEvent($request, $virtualEvent->id);

        return view('employer.virtual-events.edit', [
            'event' => $virtualEvent,
            'platforms' => $this->platforms(),
        ]);
    }

    public function update(Request $request, VirtualEvent $virtualEvent): RedirectResponse
    {
        $virtualEvent = $this->findEmployerVirtualEvent($request, $virtualEvent->id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'platform' => ['required', 'string', 'max:255'],
            'meeting_link' => ['required', 'url'],
            'registration_deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:upcoming,ongoing,completed,cancelled'],
        ], [
            'end_time.after' => __('End time must be after start time.'),
        ]);

        // Custom validation: registration_deadline must be before or on event date
        if (!empty($validated['registration_deadline']) && !empty($validated['date'])) {
            $deadline = \Carbon\Carbon::parse($validated['registration_deadline']);
            $eventDate = \Carbon\Carbon::parse($validated['date']);
            if ($deadline->isAfter($eventDate)) {
                return redirect()->back()
                    ->withErrors(['registration_deadline' => __('Registration deadline must be before or on the event date.')])
                    ->withInput();
            }
        }

        $virtualEvent->update($validated);

        return redirect()->route('employer.virtual-events.show', $virtualEvent)
            ->with('success', __('Virtual event updated successfully.'));
    }

    public function cancel(Request $request, VirtualEvent $virtualEvent): RedirectResponse
    {
        $virtualEvent = $this->findEmployerVirtualEvent($request, $virtualEvent->id);

        if ($virtualEvent->isCancelled()) {
            return redirect()->route('employer.virtual-events.show', $virtualEvent)
                ->with('success', __('This event is already cancelled.'));
        }

        if ($virtualEvent->isCompleted()) {
            return redirect()->route('employer.virtual-events.show', $virtualEvent)
                ->withErrors(['error' => __('Cannot cancel an event that has already been completed.')]);
        }

        $virtualEvent->update(['status' => 'cancelled']);

        return redirect()->route('employer.virtual-events.show', $virtualEvent)
            ->with('success', __('Virtual event has been cancelled.'));
    }

    public function destroy(Request $request, VirtualEvent $virtualEvent): RedirectResponse
    {
        $virtualEvent = $this->findEmployerVirtualEvent($request, $virtualEvent->id);
        $virtualEvent->delete();

        return redirect()->route('employer.virtual-events.index')
            ->with('success', __('Virtual event deleted successfully.'));
    }

    private function requireEmployer(Request $request)
    {
        $employer = $request->user()->employer;

        if (!$employer) {
            abort(403);
        }

        return $employer;
    }

    private function findEmployerVirtualEvent(Request $request, int $eventId): VirtualEvent
    {
        $employer = $this->requireEmployer($request);

        return VirtualEvent::where('employer_id', $employer->id)
            ->where('id', $eventId)
            ->firstOrFail();
    }

    private function platforms(): array
    {
        return [
            'Zoom' => 'Zoom',
            'Google Meet' => 'Google Meet',
            'Microsoft Teams' => 'Microsoft Teams',
            'Webex' => 'Webex',
            'Other' => 'Other',
        ];
    }

    private function notifyAllJobseekers(VirtualEvent $event): void
    {
        // Get all users with jobseeker role who have verified emails
        $users = \App\Models\User::where('role', UserRole::Jobseeker)
            ->whereNotNull('email_verified_at')
            ->get();

        Log::info("Found " . $users->count() . " jobseeker users to notify about virtual event {$event->id}");

        $notifiedCount = 0;

        foreach ($users as $user) {
            try {
                // Use Laravel's notifyNow() method like ApplicationStatusUpdated uses notify()
                // VirtualEventCreated only returns ['database'] in via(), so NO emails will be sent
                // This ensures notifications appear in dropdown just like application status updates
                $user->notifyNow(new VirtualEventCreated($event));
                
                $notifiedCount++;
                Log::info("Notification created for user {$user->id} (email: {$user->email}) - Database only, NO EMAIL");
            } catch (\Exception $e) {
                Log::error("Failed to notify user {$user->id} about virtual event {$event->id}: " . $e->getMessage());
                Log::error("Exception trace: " . $e->getTraceAsString());
            }
        }

        Log::info("Virtual event {$event->id} notifications sent to {$notifiedCount} jobseekers (total users found: " . $users->count() . ")");
    }
}
