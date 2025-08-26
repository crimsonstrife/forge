<?php

namespace Database\Seeders;

use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IssueEnumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Statuses
        $statuses = [
            ['name'=>'Backlog','key'=>'BACKLOG','order'=>10,'is_done'=>false,'color'=>'#64748b'],
            ['name'=>'To Do','key'=>'TODO','order'=>20,'is_done'=>false,'color'=>'#6b7280'],
            ['name'=>'In Progress','key'=>'INPROGRESS','order'=>30,'is_done'=>false,'color'=>'#2563eb'],
            ['name'=>'In Review','key'=>'INREVIEW','order'=>40,'is_done'=>false,'color'=>'#a855f7'],
            ['name'=>'Done','key'=>'DONE','order'=>90,'is_done'=>true,'color'=>'#16a34a'],
            ['name'=>'Closed','key'=>'CLOSED','order'=>95,'is_done'=>true,'color'=>'#059669'],
        ];
        foreach ($statuses as $s) {
            IssueStatus::updateOrCreate(['key'=>$s['key']], $s);
        }

        // Types
        $types = [
            ['name'=>'Epic','key'=>'EPIC', 'is_default'=>false,'is_hierarchical'=>true,'icon'=>'lucide-goal'],
            ['name'=>'Story','key'=>'STORY', 'is_default'=>false,'is_hierarchical'=>false,'icon'=>'lucide-book-open'],
            ['name'=>'Task','key'=>'TASK', 'is_default'=>true,'is_hierarchical'=>false,'icon'=>'lucide-check-square'],
            ['name'=>'Sub-task','key'=>'SUBTASK', 'is_default'=>false,'is_hierarchical'=>false,'icon'=>'lucide-square'],
            ['name'=>'Bug','key'=>'BUG', 'is_default'=>false,'is_hierarchical'=>false,'icon'=>'lucide-bug'],
        ];
        foreach ($types as $t) {
            IssueType::updateOrCreate(['key'=>$t['key']], $t);
        }

        // Priorities
        $priorities = [
            ['name'=>'Highest','key'=>'HIGHEST','order'=>10,'weight'=>100,'color'=>'#b91c1c','icon'=>'lucide-arrow-up'],
            ['name'=>'High','key'=>'HIGH','order'=>20,'weight'=>80,'color'=>'#ef4444','icon'=>'lucide-chevron-up'],
            ['name'=>'Medium','key'=>'MEDIUM','order'=>30,'weight'=>50,'color'=>'#f59e0b','icon'=>'lucide-minimize-2'],
            ['name'=>'Low','key'=>'LOW','order'=>40,'weight'=>20,'color'=>'#10b981','icon'=>'lucide-chevron-down'],
            ['name'=>'Lowest','key'=>'LOWEST','order'=>50,'weight'=>0,'color'=>'#059669','icon'=>'lucide-arrow-down'],
        ];
        foreach ($priorities as $p) {
            IssuePriority::updateOrCreate(['key'=>$p['key']], $p);
        }
    }
}
