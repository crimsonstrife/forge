<?php

namespace App\Models;

use App\Enums\IssueTier;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    use IsPermissible;

    protected $fillable = [
        'name',
        'key',
        'tier',
        'is_hierarchical',
        'is_default',
        'icon'
    ];

    protected $casts = [
        'tier' => IssueTier::class,
        'is_hierarchical'=>'bool',
        'is_default' => 'bool'
    ];

    /**
     * @return array<int, IssueTier>
     */
    public function allowedChildTiers(): array
    {
        // Your rules:
        // - Epic: can contain anything except Epics.
        // - Story: can contain anything except Epics.
        // - Task: only Sub-Tasks.
        // - Sub-Task: can contain additional sub-tasks.
        // - Other: behaves like Task (only Sub-Tasks).
        return match ($this->tier) {
            IssueTier::Epic  => [IssueTier::Story, IssueTier::Task, IssueTier::SubTask, IssueTier::Other],
            IssueTier::Story => [IssueTier::Task, IssueTier::SubTask, IssueTier::Other],
            IssueTier::Task, IssueTier::SubTask, IssueTier::Other => [IssueTier::SubTask],
        };
    }

    public function allowsChildType(IssueType $child): bool
    {
        return in_array($child->tier, $this->allowedChildTiers(), true);
    }
}
