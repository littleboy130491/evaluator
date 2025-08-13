<?php

namespace Database\Factories;

use App\Models\GroupArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupArea>
 */
class GroupAreaFactory extends Factory
{
    protected $model = GroupArea::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true) . ' Region',
            'description' => $this->faker->sentence(),
        ];
    }
}