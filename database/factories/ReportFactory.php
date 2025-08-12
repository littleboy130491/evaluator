<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['evaluation_summary', 'outlet_performance', 'evaluator_activity']),
            'parameters' => [
                'start_date' => $this->faker->date(),
                'end_date' => $this->faker->date(),
                'outlet_id' => $this->faker->optional()->numberBetween(1, 10),
            ],
            'user_id' => User::factory(),
            'file_path' => $this->faker->optional()->filePath(),
            'generated_at' => $this->faker->optional()->dateTimeThisYear(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}