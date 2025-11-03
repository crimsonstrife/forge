<?php

namespace App\Notifications;

use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
class IssueAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Issue $issue,
        public string $url,
    ) {
        // Make sure the queued notification only runs after DB commit
        $this->afterCommit = true;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * @return array{issue_id:string, project_id: ?string, summary: ?string, url:string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'issue_id'   => (string) $this->issue->getKey(),
            'project_id' => $this->issue->project_id,
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

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
