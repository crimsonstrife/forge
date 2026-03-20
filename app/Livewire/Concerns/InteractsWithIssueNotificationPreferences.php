<?php

namespace App\Livewire\Concerns;

use App\Models\IssueNotificationPreference;
use App\Models\User;

trait InteractsWithIssueNotificationPreferences
{
    public bool $notify_on_assignment = true;

    public bool $notify_on_comment = true;

    public bool $notify_on_status_change = true;

    public bool $notify_on_link_change = true;

    public bool $notify_on_mention = true;

    public bool $daily_digest_enabled = false;

    protected function loadIssueNotificationPreferences(User $user): void
    {
        $preferences = $user->issueNotificationPreference()->firstOrCreate([]);

        $this->notify_on_assignment = (bool) $preferences->notify_on_assignment;
        $this->notify_on_comment = (bool) $preferences->notify_on_comment;
        $this->notify_on_status_change = (bool) $preferences->notify_on_status_change;
        $this->notify_on_link_change = (bool) $preferences->notify_on_link_change;
        $this->notify_on_mention = (bool) $preferences->notify_on_mention;
        $this->daily_digest_enabled = (bool) $preferences->daily_digest_enabled;
    }

    protected function saveIssueNotificationPreferences(User $user): IssueNotificationPreference
    {
        return $user->issueNotificationPreference()->updateOrCreate([], $this->issueNotificationPreferenceState());
    }

    /**
     * @return array<string, bool>
     */
    protected function issueNotificationPreferenceState(): array
    {
        return [
            'notify_on_assignment' => $this->notify_on_assignment,
            'notify_on_comment' => $this->notify_on_comment,
            'notify_on_status_change' => $this->notify_on_status_change,
            'notify_on_link_change' => $this->notify_on_link_change,
            'notify_on_mention' => $this->notify_on_mention,
            'daily_digest_enabled' => $this->daily_digest_enabled,
        ];
    }
}
