<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator<Organization> $organizations
 */
final class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    #[Url]
    public string $q = '';

    public function render(): View
    {
        $this->authorize('viewAny', Organization::class);

        $query = Organization::query()
            ->when($this->q !== '', static fn ($q) => $q->where('name', 'like', '%'.$this->q.'%'))
            ->orderBy('name');

        return view('livewire.organizations.index', [
            'organizations' => $query->paginate(10),
        ]);
    }

    public function delete(string $id): void
    {
        $org = Organization::query()->findOrFail($id);

        $this->authorize('delete', $org);

        $org->delete();

        session()->flash('status', 'Organization moved to trash.');
        $this->resetPage();
    }
}
