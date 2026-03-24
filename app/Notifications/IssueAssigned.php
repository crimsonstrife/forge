<?php

namespace App\Notifications;

use App\Models\Issue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @phpstan-type IssueData array{
 *   issue_id: string,
 *   project_id: string|null,
 *   summary: string|null,
 *   url: string
 * }
 */
class IssueAssigned extends Notification
{
    public function __construct(
        public Issue $issue,
        public string $url,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'assignment',
            'issue_id'   => (string) $this->issue->getKey(),
            'issue_key'  => (string) $this->issue->key,
            'project_id' => $this->issue->project_id,
            'title'      => __('Assigned to :issue', ['issue' => $this->issue->key]),
            'summary'    => $this->issue->summary,
            'url'        => $this->url,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('New assignment: :summary', ['summary' => $this->issue->summary ?? __('Issue')]))
            ->line(__('You were assigned to an issue.'))
            ->action(__('View Issue'), $this->url);
    }
}
