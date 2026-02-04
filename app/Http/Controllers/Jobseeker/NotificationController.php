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

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->route('jobseeker.notifications');
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

        if ($applicationId) {
            return redirect()->route('jobseeker.history.show', [$applicationId, 'from' => 'notifications']);
        }

        if ($docType) {
            return redirect()->route('jobseeker.documents');
        }

        return redirect()->route('jobseeker.notifications');
    }
}
