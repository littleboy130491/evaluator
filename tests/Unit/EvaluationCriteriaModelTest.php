<?php

namespace Tests\Unit;

use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationCriteriaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_evaluation_criteria(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $this->assertDatabaseHas('evaluation_criteria', [
            'id' => $criteria->id,
            'name' => $criteria->name,
            'max_score' => $criteria->max_score,
        ]);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $criteria = EvaluationCriteria::factory()->create();
        $criteria->delete();

        $this->assertSoftDeleted('evaluation_criteria', ['id' => $criteria->id]);
    }

    /** @test */
    public function it_has_scores_relationship(): void
    {
        $criteria = EvaluationCriteria::factory()->create();
        $score = EvaluationCriteriaScore::factory()->create(['evaluation_criteria_id' => $criteria->id]);

        $this->assertTrue($criteria->scores->contains($score));
        $this->assertInstanceOf(EvaluationCriteriaScore::class, $criteria->scores->first());
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $criteria = EvaluationCriteria::factory()->create([
            'is_active' => true,
            'max_score' => 10,
        ]);

        $this->assertIsBool($criteria->is_active);
        $this->assertIsInt($criteria->max_score);
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        // Attempt to create criteria without required fields
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        EvaluationCriteria::create([
            // Missing 'name' and 'max_score'
            'description' => 'Test description',
        ]);
    }
}