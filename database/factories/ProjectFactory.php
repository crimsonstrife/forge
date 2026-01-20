<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'key' => strtoupper(Str::random(4)),
            'description' => fake()->sentence(),
            'organization_id' => Organization::factory(),
            'lead_id' => User::factory(),
        ];
    }
}
