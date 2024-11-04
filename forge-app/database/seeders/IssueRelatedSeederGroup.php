<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Issues; // Import the Issues seeder group.

class IssueRelatedSeederGroup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the Issue Priorities
        $this->call(Issues\IssuePrioritySeeder::class);
        // Seed the Priority Sets
        $this->call(Issues\PrioritySetSeeder::class);
        // Seed the Issue Types
        $this->call(Issues\IssueTypeSeeder::class);
        // Seed the Issue Statuses
        $this->call(Issues\IssueStatusSeeder::class);
    }
}
