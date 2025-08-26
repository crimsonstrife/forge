<?php

namespace App\Rules;

use App\Enums\ProjectSet;
use App\Models\Project;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

final class BelongsToProjectSet implements ValidationRule
{
    public function __construct(private readonly Project $project, private readonly ProjectSet $set) {
        if (! $project || ! $set instanceof ProjectSet) {
            throw new InvalidArgumentException('Invalid Project or ProjectSet provided.');
        }
    }

    /**
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $ok = match ($this->set) {
            ProjectSet::Type     => in_array((string) $value, $this->project->allowedTypeIds(), true),
            ProjectSet::Status   => in_array((string) $value, $this->project->allowedStatusIds(), true),
            ProjectSet::Priority => in_array((string) $value, $this->project->allowedPriorityIds(), true),
        };
        if (! $ok) {
            $fail(__('The selected :attribute is not allowed for this project.', ['attribute' => $attribute]));
        }
    }
}
