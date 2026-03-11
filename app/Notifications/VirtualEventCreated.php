<?php

namespace App\Notifications;

use App\Models\VirtualEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Virtual event created notification.
 *
 * In-app only (jobseeker account). No email is sent.
 */
class VirtualEventCreated extends Notification
{
    use Queueable;

    public function __construct(private readonly VirtualEvent $virtualEvent)
    {
    }

    /** In-app only; no mail channel. */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'virtual_event_id' => $this->virtualEvent->id,
            'title' => $this->virtualEvent->title,
            'date' => $this->virtualEvent->date->format('M d, Y'),
            'start_time' => $this->virtualEvent->start_time,
            'platform' => $this->virtualEvent->platform,
            'type' => 'virtual_event_created',
        ];
    }
}
