<?php

namespace App\Livewire\Today;

use App\Models\Issue;
use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Solo dashboard showing current running timer and on-deck issues for the user.
 */
final class TodayPanel extends Component
{
    #[On('timer-started')]
    #[On('timer-stopped')]
    public function refresh(): void
    {
        // No-op; event hooks force re-render.
    }

    public function render(): View
    {
        $userId = (string) Auth::id();

        $running = TimeEntry::query()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->with(['issue:id,summary,project_id,key,issue_status_id', 'issue.status:id,name,color,is_done'])
            ->latest('started_at')
            ->first();

        // Pinned “Next”
        $pinned = Issue::query()
            ->select(['id','summary','project_id','issue_status_id','updated_at','is_next'])
            ->where('assignee_id', $userId)
            ->where('is_next', true)
            ->whereRelation('status', 'is_done', false)
            ->with(['status:id,name,color', 'project:id,key'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // On Deck (not pinned)
        $onDeck = Issue::query()
            ->select(['id','summary','project_id','issue_status_id','updated_at','is_next'])
            ->where('assignee_id', $userId)
            ->where('is_next', false)
            ->whereRelation('status', 'is_done', false)
            ->with(['status:id,name,color', 'project:id,key'])
            ->latest('updated_at')
            ->limit(15)
            ->get();

        return view('livewire.today.today-panel', [
            'running' => $running,
            'onDeck'  => $onDeck,
            'pinned'  => $pinned,
        ]);
    }
}
