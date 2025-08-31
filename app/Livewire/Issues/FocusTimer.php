<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\TimeEntry;
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
        $this->authorize('update', $this->issue);

        if ($this->hasAnyRunningTimer(Auth::id())) {
            $this->dispatch('banner-message', type: 'warning', message: 'You already have a running timer. Stop it first.');
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

