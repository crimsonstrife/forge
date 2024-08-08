<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use GuzzleHttp\Promise\Is;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private Issue $issue;

    /**
     * Create a new notification instance.
     *
     * @param Issue $issue
     * @return void
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Get the notification's delivery channels.
     * @param mixed $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        //get the channels
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line(__('A new issue has just been created.'))
            ->line('- ' . __('Issue name:') . ' ' . $this->issue->name)
            ->line('- ' . __('Project:') . ' ' . $this->issue->project->name)
            ->line('- ' . __('Owner:') . ' ' . $this->issue->owner->name)
            ->line('- ' . __('Responsible:') . ' ' . $this->issue->responsible?->name ?? '-')
            ->line('- ' . __('Status:') . ' ' . $this->issue->status->name)
            ->line('- ' . __('Type:') . ' ' . $this->issue->type->name)
            ->line('- ' . __('Priority:') . ' ' . $this->issue->priority->name)
            ->line(__('See more details of this issue by clicking on the button below:'))
            ->action(__('View details'), route('filament.resources.issues.share', $this->issue->slug));
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title(__('New issue created'))
            ->icon('octicon-issue-opened')
            ->body(fn() => $this->issue->name)
            ->actions([
                Action::make('view')
                    ->link()
                    ->icon('octicon-eye')
                    ->url(fn() => route('filament.resources.issues.share', $this->issue->slug)),
            ])
            ->getDatabaseMessage();
    }
}
