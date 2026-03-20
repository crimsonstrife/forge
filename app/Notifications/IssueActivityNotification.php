<?php

namespace App\Notifications;

use App\Models\Issue;
use Illuminate\Notifications\Notification;

class IssueActivityNotification extends Notification
{
    public function __construct(
        public Issue $issue,
        public string $event,
        public string $title,
        public string $summary,
        public string $url,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'issue_id' => (string) $this->issue->getKey(),
            'issue_key' => (string) $this->issue->key,
            'project_id' => $this->issue->project_id,
            'title' => $this->title,
            'summary' => $this->summary,
            'url' => $this->url,
        ];
    }
}
