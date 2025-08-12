<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationCriteriaScoreModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_criteria_score(): void
    {
        $score = EvaluationCriteriaScore::factory()->create();

        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'id' => $score->id,
            'evaluation_id' => $score->evaluation_id,
            'evaluation_criteria_id' => $score->evaluation_criteria_id,
            'score' => $score->score,
        ]);
    }

    /** @test */
    public function it_has_evaluation_relationship(): void
    {
        $evaluation = Evaluation::factory()->create();
        $score = EvaluationCriteriaScore::factory()->create(['evaluation_id' => $evaluation->id]);

        $this->assertEquals($evaluation->id, $score->evaluation->id);
        $this->assertInstanceOf(Evaluation::class, $score->evaluation);
    }

    /** @test */
    public function it_has_criteria_relationship(): void
    {
        $criteria = EvaluationCriteria::factory()->create();
        $score = EvaluationCriteriaScore::factory()->create(['evaluation_criteria_id' => $criteria->id]);

        $this->assertEquals($criteria->id, $score->criteria->id);
        $this->assertInstanceOf(EvaluationCriteria::class, $score->criteria);
    }

    /** @test */
    public function it_casts_score_as_integer(): void
    {
        $score = EvaluationCriteriaScore::factory()->create(['score' => '5']);

        $this->assertIsInt($score->score);
        $this->assertEquals(5, $score->score);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        // Attempt to create a score without required fields
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        EvaluationCriteriaScore::create([
            // Missing 'evaluation_id', 'evaluation_criteria_id', and 'score'
            'notes' => 'Test notes',
        ]);
    }
}