<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DocumentUpdateRequested extends Notification
{
    use Queueable;

    public function __construct(private readonly Document $document)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $documentLabel = Str::of($this->document->type)->replace('_', ' ')->title();

        $mail = (new MailMessage)
            ->subject(__('Document update requested: :document', ['document' => $documentLabel]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your :document document needs an update.', ['document' => $documentLabel]));

        if ($this->document->remarks) {
            $mail->line(__('Remarks: :remarks', ['remarks' => $this->document->remarks]));
        }

        return $mail->action(__('Update Document'), route('jobseeker.documents'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_type' => $this->document->type,
            'remarks' => $this->document->remarks,
            'type' => 'document_update_requested',
        ];
    }
}
