<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * UserCreatedNotification class.
 *
 * This class represents a notification that is sent when a user is created.
 * It extends the Notification class and implements the ShouldQueue interface.
 * The notification is queued for delivery using the Queueable trait.
 *
 * @package App\Notifications
 */
class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject(__('Validate your account'))->line(__('Welcome to :app platform.', ['app' => config('app.name')]))->line(__('To complete the creation of your account, please use the below button to choose a password and verify your user account.'))->action(__('Verify my account'), route('validate-account', $this->user->creation_token));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
