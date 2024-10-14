<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Issues\Issue;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * IssueCommented Notification
 * Represents a notification sent when a new comment is added to an issue.
 *
 * @package App\Notifications
 */
class IssueCommented extends Notification implements ShouldQueue
{
    use Queueable;

    public Issue $issue;

    private Comment $comment;

    /**
     * Create a new notification instance.
     *
     * @param Comment $issueComment
     * @return void
     */
    public function __construct(Comment $issueComment)
    {
        $this->comment = $issueComment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line(
                __(
                    'A new comment has been added to the issue :issue by :name.',
                    [
                        'issue' => $this->comment->issue->name,
                        'name' => $this->comment->user->name
                    ]
                )
            )
            ->line(__('See more details of this issue by clicking on the button below:'))
            ->action(
                __('View details'),
                route('filament.resources.issues.share', $this->comment->issue->code)
            );
    }


    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title(
                __(
                    'Issue :issue commented',
                    [
                        'issue' => $this->comment->issue->name
                    ]
                )
            )
            ->icon('octicon-comment')
            ->body(fn () => __('by :name', ['name' => $this->comment->user->name]))
            ->actions([
                Action::make('view')
                    ->link()
                    ->icon('octicon-eye')
                    ->url(fn () => route('filament.resources.issues.share', $this->comment->issue->code)),
            ])
            ->getDatabaseMessage();
    }
}
