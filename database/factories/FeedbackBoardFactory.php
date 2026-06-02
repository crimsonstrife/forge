<?php

namespace Database\Factories;

use App\Models\FeedbackBoard;
use App\Models\ServiceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedbackBoardFactory extends Factory
{
    protected $model = FeedbackBoard::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'service_product_id' => ServiceProduct::factory(),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'name' => $name,
            'description' => fake()->sentence(),
            'public_url' => 'https://example.test/feedback',
            'is_public' => true,
            'allow_anonymous_read' => true,
            'default_sort' => 'top',
        ];
    }
}
