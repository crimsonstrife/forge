<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Displays tracked time for an Issue with totals and an entry list.
 */
final class TimeEntriesPanel extends Component
{
    use AuthorizesRequests;

    public Issue $issue;

    /** @var bool Show all users’ entries vs only mine */
    public bool $showAll = false;

    /** @var array<int, array{
     *   id: string,
     *   user: string,
     *   started_at: string,
     *   ended_at: ?string,
     *   duration_seconds: int,
     *   is_running: bool,
     *   source: string,
     *   notes: ?string
     * }>
     */
    public array $entries = [];

    public int $totalSeconds = 0;

    public ?string $editingId = null;
    public string $editingNotes = '';

    public function mount(Issue $issue): void
    {
        $this->authorize('view', $issue);
        $this->issue = $issue;
        $this->refreshData();
    }

    public function render(): View
    {
        return view('livewire.issues.time-entries-panel');
    }

    public function toggleScope(): void
    {
        $this->showAll = ! $this->showAll;
        $this->refreshData();
    }

    #[On('timer-started')]
    #[On('timer-stopped')]
    public function refreshData(): void
    {
        $query = TimeEntry::query()
            ->where('issue_id', $this->issue->id)
            ->with(['user:id,name'])
            ->orderByDesc('started_at');

        if (! $this->showAll) {
            $query->where('user_id', Auth::id());
        }

        $rows = $query->get();

        $this->entries = $rows->map(function (TimeEntry $t): array {
            $isRunning = $t->ended_at === null;

            $duration = $isRunning
                ? max(0, now()->getTimestamp() - $t->started_at->getTimestamp())
                : (int) $t->duration_seconds;

            return [
                'id' => $t->id,
                'user' => optional($t->user)->name ?? '—',
                'started_at' => $t->started_at->toDateTimeString(),
                'ended_at' => $t->ended_at?->toDateTimeString(),
                'duration_seconds' => $duration,
                'is_running' => $isRunning,
                'source' => $t->source,
                'notes' => $t->notes,
            ];
        })->values()->all();

        // Total (includes running)
        $this->totalSeconds = array_sum(array_map(
            fn (array $r): int => $r['duration_seconds'],
            $this->entries
        ));
    }

    /**
     * Allow user (or privileged role) to stop a running entry from the list.
     */
    public function stopEntry(string $entryId): void
    {
        $entry = TimeEntry::query()
            ->where('id', $entryId)
            ->where('issue_id', $this->issue->id)
            ->first();

        if (! $entry || $entry->ended_at !== null) {
            return;
        }

        // Allow owner of entry or anyone who can update the issue
        if ($entry->user_id !== Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $entry->finalizeNow();
        $this->refreshData();
        $this->dispatch('timer-stopped'); // keep everything in sync visually
    }

    public function edit(string $entryId): void
    {
        $entry = TimeEntry::query()
            ->where('issue_id', $this->issue->id)
            ->where('id', $entryId)
            ->with('user:id,name')
            ->first();

        if (! $entry) {
            return;
        }

        if ($entry->user_id !== Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $this->editingId = $entryId;
        $this->editingNotes = $entry->notes ?? '';
    }

    public function saveNote(): void
    {
        if (! $this->editingId) {
            return;
        }

        $entry = TimeEntry::query()
            ->where('issue_id', $this->issue->id)
            ->where('id', $this->editingId)
            ->first();

        if (! $entry) {
            return;
        }

        if ($entry->user_id !== Auth::id()) {
            $this->authorize('update', $this->issue);
        }

        $entry->notes = trim($this->editingNotes) !== '' ? $this->editingNotes : null;
        $entry->save();

        $this->editingId = null;
        $this->editingNotes = '';
        $this->refreshData();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingNotes = '';
    }
}
