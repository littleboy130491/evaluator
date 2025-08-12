<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationCriteriaScore>
 */
class EvaluationCriteriaScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationCriteriaScore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'evaluation_criteria_id' => EvaluationCriteria::factory(),
            'score' => function (array $attributes) {
                $criteria = EvaluationCriteria::find($attributes['evaluation_criteria_id']);
                return $this->faker->numberBetween(1, $criteria ? $criteria->max_score : 10);
            },
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}