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

    /**
     * Returns a Bootstrap-friendly color (or hex) for this tier.
     */
    public function badgeColor(): string
    {
        return match ($this->tier?->value) {
            'epic'     => '#7e57c2', // purple
            'story'    => '#1e88e5', // blue
            'task'     => '#9e9e9e', // gray
            'subtask'  => '#78909C', // blue-gray (subtle)
            default    => '#607D8B', // fallback
        };
    }

    /**
     * Returns an icons name to use on the front-end.
     */
    public function iconName(): string
    {
        return match ($this->tier?->value) {
            'epic'     => 'all_inclusive',    // ∞ vibe
            'story'    => 'menu_book',
            'task'     => 'check_box',
            'subtask'  => 'subdirectory_arrow_right',
            default    => 'filter_none',
        };
    }
}
