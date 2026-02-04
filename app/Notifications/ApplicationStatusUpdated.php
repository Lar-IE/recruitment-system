<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Application $application,
        private readonly ?string $note,
        private readonly ?string $interviewAt,
        private readonly ?string $interviewLink
    )
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->jobPost->title ?? 'Job';
        $status = ucfirst($this->application->current_status);

        $mail = (new MailMessage)
            ->subject(__('Application update: :job', ['job' => $jobTitle]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your application for :job has been updated.', ['job' => $jobTitle]))
            ->line(__('Status: :status', ['status' => $status]));

        if ($this->note) {
            $mail->line(__('Note: :note', ['note' => $this->note]));
        }

        if ($this->application->current_status === 'interview_scheduled') {
            if ($this->interviewAt) {
                $mail->line(__('Interview schedule: :date', [
                    'date' => Carbon::parse($this->interviewAt)->format('M d, Y h:i A'),
                ]));
            }

            if ($this->interviewLink) {
                $mail->line(__('Interview link: :link', ['link' => $this->interviewLink]));
            }
        }

        return $mail->action(__('View Application'), route('jobseeker.history.show', $this->application));
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
            'interview_at' => $this->interviewAt,
            'interview_link' => $this->interviewLink,
            'employer' => $employerName,
        ];
    }
}
