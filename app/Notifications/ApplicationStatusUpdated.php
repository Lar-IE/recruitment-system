<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(private readonly Application $application, private readonly ?string $note)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->application->jobPost->title ?? 'Job';
        $employerName = $this->application->jobPost->employer->company_name
            ?? $this->application->jobPost->employer->user->name
            ?? 'Employer';

        return [
            'application_id' => $this->application->id,
            'job_title' => $jobTitle,
            'status' => $this->application->current_status,
            'note' => $this->note,
            'employer' => $employerName,
        ];
    }
}
