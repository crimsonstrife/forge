<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\issues\IssuePrioritySeeder;
use Database\Seeders\issues\PrioritySetSeeder;
use Database\Seeders\issues\IssueTypeSeeder;
use Database\Seeders\issues\IssueStatusSeeder;

/**
 * Seeder class to handle the seeding of issue-related data.
 */
class IssueRelatedSeederGroup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the Issue Priorities
        $this->call(IssuePrioritySeeder::class);
        // Seed the Priority Sets
        $this->call(PrioritySetSeeder::class);
        // Seed the Issue Types
        $this->call(IssueTypeSeeder::class);
        // Seed the Issue Statuses
        $this->call(IssueStatusSeeder::class);
    }
}
