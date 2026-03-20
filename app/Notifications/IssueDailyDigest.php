<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueDailyDigest extends Notification
{
    use Queueable;

    /**
     * @param array<int, array{title:string,summary:string,url:string,created_at:string}> $items
     */
    public function __construct(
        public array $items,
        public string $fromLabel,
        public string $toLabel,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject(__('Daily issue digest'))
            ->line(__('Here is your issue activity digest for :from to :to.', [
                'from' => $this->fromLabel,
                'to' => $this->toLabel,
            ]));

        foreach (array_slice($this->items, 0, 5) as $item) {
            $message->line('• '.$item['title'].' — '.$item['summary']);
        }

        if (count($this->items) > 5) {
            $message->line(__('Plus :count more notifications.', ['count' => count($this->items) - 5]));
        }

        return $message->action(__('Open notifications'), route('notifications.index', ['filter' => 'unread']));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'digest',
            'title' => __('Daily issue digest'),
            'summary' => __(':count updates from :from to :to', [
                'count' => count($this->items),
                'from' => $this->fromLabel,
                'to' => $this->toLabel,
            ]),
            'url' => route('notifications.index', ['filter' => 'unread']),
            'items' => $this->items,
        ];
    }
}
