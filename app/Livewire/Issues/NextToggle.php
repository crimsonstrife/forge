<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

final class NextToggle extends Component
{
    use AuthorizesRequests;

    public string $issueId;
    public bool $isNext = false;

    public function mount(string $issueId, bool $isNext): void
    {
        $this->issueId = $issueId;
        $this->isNext = $isNext;
    }

    public function render(): View
    {
        return view('livewire.issues.next-toggle');
    }

    public function toggle(): void
    {
        /** @var Issue|null $issue */
        $issue = Issue::query()->find($this->issueId);
        if (! $issue) {
            return;
        }

        $this->authorize('update', $issue);

        $issue->is_next = ! $issue->is_next;
        $issue->save();

        $this->isNext = $issue->is_next;
        $this->dispatch('issue-updated', id: $issue->id);
    }

    // keep in sync if other UI updates this row
    #[On('issue-updated')]
    public function refreshIfMatch(string $id): void
    {
        if ($id !== $this->issueId) {
            return;
        }
        $this->isNext = (bool) Issue::query()->whereKey($id)->value('is_next');
    }
}
