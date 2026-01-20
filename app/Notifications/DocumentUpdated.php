<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
        return ['database'];
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
