<?php

namespace Database\Seeders;

use App\Models\FeedbackBoard;
use Illuminate\Database\Seeder;

class FeedbackDefaultsSeeder extends Seeder
{
    /** @var array<int, array{name:string,slug:string,color:string,is_terminal:bool,is_default:bool}> */
    private array $statuses = [
        ['name' => 'Open', 'slug' => 'open', 'color' => '#64748b', 'is_terminal' => false, 'is_default' => true],
        ['name' => 'Planned', 'slug' => 'planned', 'color' => '#2563eb', 'is_terminal' => false, 'is_default' => false],
        ['name' => 'In Progress', 'slug' => 'in-progress', 'color' => '#7c3aed', 'is_terminal' => false, 'is_default' => false],
        ['name' => 'Completed', 'slug' => 'completed', 'color' => '#16a34a', 'is_terminal' => true, 'is_default' => false],
        ['name' => 'Declined', 'slug' => 'declined', 'color' => '#dc2626', 'is_terminal' => true, 'is_default' => false],
        ['name' => 'Duplicate', 'slug' => 'duplicate', 'color' => '#f97316', 'is_terminal' => true, 'is_default' => false],
    ];

    public function run(?FeedbackBoard $board = null): void
    {
        if ($board !== null) {
            $this->seed($board);
        }
    }

    public function seed(FeedbackBoard $board): void
    {
        foreach ($this->statuses as $position => $status) {
            $board->statuses()->firstOrCreate(
                ['slug' => $status['slug']],
                [
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'is_terminal' => $status['is_terminal'],
                    'is_default' => $status['is_default'],
                    'position' => $position,
                ]
            );
        }
    }
}
