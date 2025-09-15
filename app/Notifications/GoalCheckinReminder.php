<?php

namespace App\Notifications;

use App\Models\Goal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class GoalCheckinReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Goal $goal)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Goal check-in reminder: '.$this->goal->name)
            ->line('It’s time to add a check-in for “'.$this->goal->name.'”.')
            ->action('Open Goal', route('goals.show', $this->goal));
    }

    public function toArray(object $notifiable): array
    {
        return ['goal_id' => $this->goal->id, 'name' => $this->goal->name];
    }
}
