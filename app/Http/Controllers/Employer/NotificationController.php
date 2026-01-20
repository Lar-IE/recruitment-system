<?php

namespace App\Http\Controllers\Employer;

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

        return view('employer.notifications.index', [
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

        $data = $notification->data;

        if (($data['type'] ?? '') === 'application_submitted') {
            return redirect()->route('employer.ats', [
                'job_post_id' => $data['job_post_id'] ?? null,
            ]);
        }

        if (($data['type'] ?? '') === 'document_updated') {
            return redirect()->route('employer.documents.show', $data['jobseeker_id'] ?? 0);
        }

        return redirect()->route('employer.notifications');
    }
}
