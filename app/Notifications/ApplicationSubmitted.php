<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->jobPost->title ?? 'Job';
        $applicant = $this->application->jobseeker->user->name ?? 'Applicant';

        return (new MailMessage)
            ->subject(__('New application for :job', ['job' => $jobTitle]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('You received a new application for :job.', ['job' => $jobTitle]))
            ->line(__('Applicant: :applicant', ['applicant' => $applicant]))
            ->action(__('View Applicants'), route('employer.ats', [
                'job_post_id' => $this->application->job_post_id,
            ]));
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
