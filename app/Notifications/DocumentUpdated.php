<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DocumentUpdated extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $jobseekerId,
        private readonly string $jobseekerName,
        private readonly string $documentType
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $documentLabel = Str::of($this->documentType)->replace('_', ' ')->title();

        return (new MailMessage)
            ->subject(__('Document updated: :document', ['document' => $documentLabel]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('A jobseeker has updated their :document document.', ['document' => $documentLabel]))
            ->line(__('Jobseeker: :name', ['name' => $this->jobseekerName]))
            ->action(__('View Documents'), route('employer.documents.show', $this->jobseekerId));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'jobseeker_id' => $this->jobseekerId,
            'jobseeker' => $this->jobseekerName,
            'document_type' => $this->documentType,
            'type' => 'document_updated',
        ];
    }
}
