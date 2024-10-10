<?php

namespace App\Notifications;

use App\Models\Issues\Issue;
use App\Models\Issues\IssueActivity;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class IssueStatusUpdated
 *
 * This class represents a notification that is sent when the status of an issue is updated.
 * It extends the Notification class and implements the ShouldQueue interface for queueing the notification.
 *
 * @package App\Notifications
 */
class IssueStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    private Issue $issue;
    private IssueActivity $activity;

    /**
     * Create a new notification instance.
     *
     * @param Issue $issue
     * @return void
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
        $this->activity = $issue->activities()->latest()->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
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
            ->line(__('The status of issue :issue has been updated.', ['issue' => $this->issue->name]))
            ->line('- ' . __('Old status:') . ' ' . $this->activity->oldStatus->name)
            ->line('- ' . __('New status:') . ' ' . $this->activity->newStatus->name)
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
            ->title(__('Issue status updated'))
            ->icon('octicon-issue-tracks')
            ->body(
                fn() => __('Old status: :oldStatus - New status: :newStatus', [
                    'oldStatus' => $this->activity->oldStatus->name,
                    'newStatus' => $this->activity->newStatus->name,
                ]),
            )
            ->actions([
                Action::make('view')
                    ->link()
                    ->icon('octicon-eye')
                    ->url(fn() => route('filament.resources.issues.share', $this->issue->slug)),
            ])
            ->getDatabaseMessage();
    }
}
