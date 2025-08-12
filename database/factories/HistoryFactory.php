<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\History;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = History::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $evaluation = Evaluation::factory()->create();
        
        return [
            'historiable_type' => Evaluation::class,
            'historiable_id' => $evaluation->id,
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted', 'status_changed']),
            'old_values' => ['status' => 'draft'],
            'new_values' => ['status' => 'submitted'],
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }
}