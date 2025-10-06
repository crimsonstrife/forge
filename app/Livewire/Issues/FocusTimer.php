<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\Note;
use App\Models\TimeEntry;
use App\Settings\PersonalizationSettings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

final class FocusTimer extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    public ?TimeEntry $runningEntry = null;

    public bool $isRunning = false;

    public int $elapsedSeconds = 0;

    public string $focusUrl = '';
    public string $publicUrl = '';
    public string $runningNotes = '';

    public bool $saveNoteOnStop = true;

    /**
     * @throws AuthorizationException
     */
    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);

        $this->issue = $issue->loadMissing('project');

        $this->loadRunningEntry();
        $this->elapsedSeconds = $this->currentElapsedSeconds();

        // Build the pop-out URL from bound models (no reliance on request()).
        $this->focusUrl = route('issues.focus', [
            'project' => $this->issue->project,
            'issue'   => $this->issue,
        ]);

        // Signed, view-only URL (no auth needed)
        $this->publicUrl = URL::signedRoute('issues.focus.public', [
            'project' => $this->issue->project,
            'issue' => $this->issue,
        ]);

        $this->runningNotes = $this->runningEntry?->notes ?? '';
    }

    public function render(): View
    {
        return view('livewire.issues.focus-timer');
    }

    public function start(): void
    {
        // Authorization check
        $this->authorize('update', $this->issue);

        // Prevent duplicate running timers for this user and issue
        if ($this->runningEntry !== null) {
            // Optionally, you could throw an exception or show a message
            return;
        }
        // Enforce single-timer rule: prevent starting a timer if user has any running timer
        $existingRunningEntry = TimeEntry::where('user_id', Auth::id())
            ->running()
            ->first();
        if ($existingRunningEntry !== null) {
            // Optionally, you could throw an exception or show a message
            return;
        }
        $entry = new TimeEntry([
            'issue_id' => $this->issue->id,
            'user_id' => Auth::id(),
            'started_at' => now(),
            'source' => 'timer',
            'duration_seconds' => 0,
            'notes' => $this->runningNotes ?: null,
        ]);
        $entry->save();

        $this->runningEntry = $entry;
        $this->isRunning = true;
        $this->elapsedSeconds = 0;

        $settings = app(PersonalizationSettings::class);
        if (!empty($settings->in_progress_status_id) && (int)$this->issue->issue_status_id !== (int)$settings->in_progress_status_id) {
            $this->issue->issue_status_id = (int)$settings->in_progress_status_id;
            $this->issue->save();
        }

        $this->dispatch('timer-started');
    }

    public function stop(): void
    {
        $this->authorize('update', $this->issue);

        if (! $this->runningEntry) {
            return;
        }

        // Persist latest notes before finalize
        if ($this->runningNotes !== ($this->runningEntry->notes ?? '')) {
            $this->runningEntry->notes = $this->runningNotes ?: null;
            $this->runningEntry->save();
        }

        $this->runningEntry->finalizeNow();

        // Persist a Note snapshot from the timer notes
        if ($this->saveNoteOnStop && trim((string)$this->runningNotes) !== '') {
            Note::query()->create([
                'user_id' => Auth::id(),
                'issue_id' => $this->issue->id,
                'title' => 'Work log',
                'body' => $this->runningNotes,
                'tags' => null,
            ]);
        }

        $this->isRunning = false;
        $this->elapsedSeconds = (int) $this->runningEntry->duration_seconds;
        $this->runningEntry = null;

        $this->dispatch('timer-stopped');
    }

    /** Persist notes as the user types (debounced on the view). */
    public function updatedRunningNotes(string $value): void
    {
        if (! $this->runningEntry) {
            return;
        }

        if ($this->runningEntry->user_id !== Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $this->runningEntry->notes = trim($value) !== '' ? $value : null;
        $this->runningEntry->save();
    }

    #[On('timer-tick')]
    public function tick(): void
    {
        $this->elapsedSeconds = $this->currentElapsedSeconds();

        // Push the authoritative value to the browser so Alpine can correct itself without re-init
        $this->dispatch('timer-elapsed', seconds: $this->elapsedSeconds);
    }

    private function loadRunningEntry(): void
    {
        $this->runningEntry = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->where('issue_id', $this->issue->id)
            ->whereNull('ended_at')
            ->first();

        $this->isRunning = $this->runningEntry !== null;
    }

    private function hasAnyRunningTimer(string $userId): bool
    {
        return TimeEntry::query()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->exists();
    }

    private function currentElapsedSeconds(): int
    {
        if ($this->runningEntry === null) {
            return 0;
        }

        // Monotonic, clamped to 0 to avoid negatives from any clock drift
        $now = now()->getTimestamp();
        $start = $this->runningEntry->started_at->getTimestamp();

        return max(0, $now - $start);
    }
}
