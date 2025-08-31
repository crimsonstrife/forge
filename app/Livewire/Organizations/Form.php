<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class Form extends Component
{
    use AuthorizesRequests;

    public ?Organization $organization = null;

    #[Locked]
    public bool $isEditing = false;

    public ?string $name = ''; // <-- make nullable

    public function mount(?Organization $organization = null): void
    {
        $this->organization = $organization;
        $this->isEditing    = $organization !== null;

        if ($this->isEditing) {
            $this->authorize('update', $organization);
            $this->name = (string) $organization->name;
        } else {
            $this->authorize('create', Organization::class);
            $this->name ??= ''; // <-- ensure not null after hydration
        }
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $this->isEditing
                    ? Rule::unique('organizations', 'name')->ignore($this->organization?->id)
                    : Rule::unique('organizations', 'name'),
            ],
        ];
    }

    public function save(): void
    {
        $this->name = trim((string) $this->name);
        $this->validate();

        if ($this->isEditing) {
            $this->organization->update(['name' => $this->name]);
            session()->flash('status', 'Organization updated.');
            $this->redirectRoute(
                'organizations.show',
                ['organization' => $this->organization->slug],
                navigate: true
            );
            return;
        }

        $org = Organization::query()->create(['name' => $this->name]);
        $org->refresh(); // ensures slug is present on the instance

        session()->flash('status', 'Organization created.');
        $this->redirectRoute(
            'organizations.show',
            ['organization' => $org->slug],
            navigate: true
        );
        return;
    }

}
