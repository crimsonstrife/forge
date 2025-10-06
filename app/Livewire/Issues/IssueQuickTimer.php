<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\TimeEntry;
use App\Settings\PersonalizationSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

final class IssueQuickTimer extends Component
{
    public string $issueId;

    public bool $isRunning = false;
    public ?string $runningEntryId = null;

    public function mount(string $issueId): void
    {
        $this->issueId = $issueId;
        $this->refreshRunning();
    }

    public function render(): View
    {
        return view('livewire.issues.issue-quick-timer');
    }

    public function start(): void
    {
        // Stop any other running entry for this user (single-timer rule)
        TimeEntry::query()
            ->where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        $entry = TimeEntry::query()->create([
            'issue_id' => $this->issueId,
            'user_id' => Auth::id(),
            'started_at' => now(),
            'source' => 'timer',
            'duration_seconds' => 0,
            'notes' => null,
        ]);

        // push issue to "In Progress" on start (same behavior as FocusTimer)
        $settings = app(PersonalizationSettings::class);
        if (isset($settings->in_progress_status_id) && ! empty($settings->in_progress_status_id)) {
            /** @var Issue|null $issue */
            $issue = Issue::query()->find($this->issueId);
            if (
                $issue &&
                $issue->issue_status_id !== null &&
                $settings->in_progress_status_id !== null &&
                (int)$issue->issue_status_id !== (int)$settings->in_progress_status_id
            ) {
                $issue->issue_status_id = (int)$settings->in_progress_status_id;
                $issue->save();
            }
        }

        $this->runningEntryId = $entry->id;
        $this->isRunning = true;

        $this->dispatch('timer-started');
    }

    public function stop(): void
    {
        if (! $this->isRunning || $this->runningEntryId === null) {
            return;
        }

        $entry = TimeEntry::query()->whereKey($this->runningEntryId)->first();
        if ($entry && $entry->ended_at === null) {
            $entry->finalizeNow();
        }

        $this->runningEntryId = null;
        $this->isRunning = false;

        $this->dispatch('timer-stopped');
    }

    private function refreshRunning(): void
    {
        $running = TimeEntry::query()
            ->forUser((string) Auth::id())
            ->forIssue($this->issueId)
            ->running()
            ->latest('started_at')
            ->first();

        $this->isRunning = $running !== null;
        $this->runningEntryId = $running?->id;
    }

    #[On('timer-started')]
    #[On('timer-stopped')]
    public function _refreshOnGlobalTimerEvent(): void
    {
        $this->refreshRunning();
    }
}
