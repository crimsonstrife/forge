<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    /**
     * The Comment instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Create a new notification instance.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
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
        $username = $this->model->user->username;
        $modelId = $this->model->getKey();
        $modelType = class_basename($this->model);
        $modelRoute = strtolower($modelType) . 's/' . $modelId;
        $modelLink = url('/' . $modelRoute);

        // Create the message
        $message = "<strong>@{ $username }</strong> has mentioned your name in a " . $modelType . ".<br>";

        return (new MailMessage)
                    ->line($message)
                    ->action('View', $modelLink)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        $username = $this->model->user->username;
        $modelId = $this->model->getKey();
        $modelType = class_basename($this->model);

        // Create the message
        $message = "<strong>@{ $username }</strong> has mentioned your name in a " . $modelType . ".";

        return [
            'mentionable_id' => $modelId,
            'mentionable_type' => $modelType,
            'message' => $message,
        ];
    }
}
