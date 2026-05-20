<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'key' => Str::upper(fake()->unique()->lexify('???')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
