<?php

namespace Database\Factories;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(MilestoneType::cases()),
            'state' => fake()->randomElement(MilestoneState::cases()),
            'description' => fake()->sentence(),
        ];
    }
}
