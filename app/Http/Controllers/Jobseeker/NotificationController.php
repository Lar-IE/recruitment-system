<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('jobseeker.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markReadAndRedirect(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        $applicationId = $notification->data['application_id'] ?? null;
        $docType = $notification->data['document_type'] ?? null;
        $virtualEventId = $notification->data['virtual_event_id'] ?? null;

        if ($virtualEventId) {
            return redirect()->route('jobseeker.virtual-events.show', $virtualEventId);
        }

        if ($applicationId) {
            return redirect()->route('jobseeker.history.show', [$applicationId, 'from' => 'notifications']);
        }

        if ($docType) {
            return redirect()->route('jobseeker.documents');
        }

        return redirect()->route('jobseeker.notifications');
    }
}
