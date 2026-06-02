<?php

namespace Database\Factories;

use App\Models\FeedbackBoard;
use App\Models\FeedbackCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedbackCategoryFactory extends Factory
{
    protected $model = FeedbackCategory::class;

    public function definition(): array
    {
        $name = fake()->word();

        return [
            'board_id' => FeedbackBoard::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'color' => '#64748b',
            'position' => 0,
        ];
    }
}
