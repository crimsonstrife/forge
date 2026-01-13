<?php

declare(strict_types=1);

namespace App\Http\Requests\Milestones;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project
            && $this->user() !== null
            && $this->user()->can('update', $project);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $typeValues = array_map(static fn (MilestoneType $c): string => $c->value, MilestoneType::cases());
        $stateValues = array_map(static fn (MilestoneState $c): string => $c->value, MilestoneState::cases());

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'type' => ['required', Rule::in($typeValues)],
            'state' => ['required', Rule::in($stateValues)],

            'starts_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            'version' => [
                'nullable',
                'string',
                'max:64',
                Rule::requiredIf(fn (): bool => $this->input('type') === MilestoneType::Release->value),
            ],
            'released_at' => ['nullable', 'date'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
