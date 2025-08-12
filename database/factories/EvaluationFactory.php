<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Evaluation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'outlet_id' => Outlet::factory(),
            'evaluation_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['draft', 'submitted', 'approved', 'rejected']),
            'notes' => $this->faker->optional()->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the evaluation is in draft status.
     *
     * @return static
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the evaluation is submitted.
     *
     * @return static
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the evaluation is approved.
     *
     * @return static
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => User::factory(),
                'approved_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
            ];
        });
    }

    /**
     * Indicate that the evaluation is rejected.
     *
     * @return static
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approved_by' => User::factory(),
                'approved_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
            ];
        });
    }
}