<?php

namespace App\Console\Commands;

use App\Models\IssueNotificationPreference;
use App\Notifications\IssueActivityNotification;
use App\Notifications\IssueAssigned;
use App\Notifications\IssueDailyDigest;
use App\Notifications\MentionNotification;
use Illuminate\Console\Command;

class SendIssueNotificationDigests extends Command
{
    protected $signature = 'issues:send-digests';

    protected $description = 'Send daily issue collaboration digests to users who opted in.';

    public function handle(): int
    {
        $sent = 0;

        IssueNotificationPreference::query()
            ->with('user')
            ->where('daily_digest_enabled', true)
            ->chunkById(100, function ($preferences) use (&$sent): void {
                foreach ($preferences as $preference) {
                    $user = $preference->user;
                    if (! $user) {
                        continue;
                    }

                    $since = $preference->daily_digest_last_sent_at ?? now()->subDay();
                    $until = now();

                    $notifications = $user->notifications()
                        ->whereIn('type', [
                            IssueAssigned::class,
                            IssueActivityNotification::class,
                            MentionNotification::class,
                        ])
                        ->where('created_at', '>', $since)
                        ->where('created_at', '<=', $until)
                        ->latest()
                        ->limit(25)
                        ->get();

                    if ($notifications->isEmpty()) {
                        continue;
                    }

                    $items = $notifications->map(static function ($notification): array {
                        $data = (array) ($notification->data ?? []);

                        return [
                            'title' => (string) ($data['title'] ?? class_basename($notification->type)),
                            'summary' => (string) ($data['summary'] ?? ''),
                            'url' => (string) ($data['url'] ?? route('notifications.index', ['filter' => 'unread'])),
                            'created_at' => $notification->created_at?->toIso8601String() ?? now()->toIso8601String(),
                        ];
                    })->all();

                    $user->notify(new IssueDailyDigest(
                        items: $items,
                        fromLabel: $since->format('M j, g:i A'),
                        toLabel: $until->format('M j, g:i A'),
                    ));

                    $preference->forceFill([
                        'daily_digest_last_sent_at' => $notifications->max('created_at') ?? $until,
                    ])->save();

                    $sent++;
                }
            });

        $this->info("Sent {$sent} issue digests.");

        return self::SUCCESS;
    }
}
