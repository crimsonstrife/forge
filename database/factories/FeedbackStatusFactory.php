<?php

namespace Database\Factories;

use App\Models\FeedbackBoard;
use App\Models\FeedbackStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedbackStatusFactory extends Factory
{
    protected $model = FeedbackStatus::class;

    public function definition(): array
    {
        $name = fake()->word();

        return [
            'board_id' => FeedbackBoard::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'color' => '#64748b',
            'is_terminal' => false,
            'is_default' => false,
            'position' => 0,
        ];
    }
}
