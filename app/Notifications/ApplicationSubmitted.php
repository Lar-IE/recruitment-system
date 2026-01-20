<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_post_id' => $this->application->job_post_id,
            'job_title' => $this->application->jobPost->title ?? 'Job',
            'applicant' => $this->application->jobseeker->user->name ?? 'Applicant',
            'type' => 'application_submitted',
        ];
    }
}
