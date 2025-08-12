<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CriteriaScoresRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_criteria_scores_relation_manager()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        $evaluation = Evaluation::factory()->create();
        EvaluationCriteria::factory()->count(3)->create();
        
        $response = $this->actingAs($admin)->get("/admin/evaluations/{$evaluation->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('criteria');
    }

    public function test_evaluator_can_view_criteria_scores_relation_manager()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        $evaluation = Evaluation::factory()->create(['user_id' => $evaluator->id]);
        EvaluationCriteria::factory()->count(3)->create();
        
        $response = $this->actingAs($evaluator)->get("/admin/evaluations/{$evaluation->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('criteria');
    }

    public function test_manager_can_view_criteria_scores_relation_manager()
    {
        $this->seed();
        
        $manager = User::where('email', 'manager@example.com')->first();
        $evaluation = Evaluation::factory()->create();
        EvaluationCriteria::factory()->count(3)->create();
        
        $response = $this->actingAs($manager)->get("/admin/evaluations/{$evaluation->id}/edit");
        
        $response->assertStatus(200);
        $response->assertSee('criteria');
    }

    public function test_cannot_create_duplicate_criteria_score_via_database_constraint()
    {
        $this->seed();
        
        $evaluation = Evaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create();
        
        // Create first score - should succeed
        EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 85,
            'notes' => 'First score',
        ]);
        
        // Try to create duplicate - should fail with database constraint
        $this->expectException(QueryException::class);
        
        EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 90,
            'notes' => 'Duplicate score',
        ]);
    }

    public function test_can_create_scores_for_different_criteria_same_evaluation()
    {
        $this->seed();
        
        $evaluation = Evaluation::factory()->create();
        $criteria1 = EvaluationCriteria::factory()->create(['name' => 'Service Quality']);
        $criteria2 = EvaluationCriteria::factory()->create(['name' => 'Cleanliness']);
        
        // Create score for first criteria - should succeed
        $score1 = EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria1->id,
            'score' => 85,
            'notes' => 'Good service',
        ]);
        
        // Create score for second criteria - should also succeed
        $score2 = EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria2->id,
            'score' => 90,
            'notes' => 'Very clean',
        ]);
        
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'id' => $score1->id,
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria1->id,
        ]);
        
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'id' => $score2->id,
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria2->id,
        ]);
    }

    public function test_can_create_same_criteria_for_different_evaluations()
    {
        $this->seed();
        
        $evaluation1 = Evaluation::factory()->create();
        $evaluation2 = Evaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create(['name' => 'Service Quality']);
        
        // Create score for first evaluation - should succeed
        $score1 = EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation1->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 85,
            'notes' => 'Good service eval 1',
        ]);
        
        // Create score for second evaluation with same criteria - should succeed
        $score2 = EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluation2->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 90,
            'notes' => 'Good service eval 2',
        ]);
        
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'id' => $score1->id,
            'evaluation_id' => $evaluation1->id,
            'evaluation_criteria_id' => $criteria->id,
        ]);
        
        $this->assertDatabaseHas('evaluation_criteria_scores', [
            'id' => $score2->id,
            'evaluation_id' => $evaluation2->id,
            'evaluation_criteria_id' => $criteria->id,
        ]);
    }

    public function test_evaluation_service_prevents_duplicates_via_update()
    {
        $this->seed();
        
        $evaluation = Evaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create(['max_score' => 100]); // Set high max_score
        $service = app(EvaluationService::class);
        
        // Create first score via service - should succeed
        $score1 = $service->scoreCriteria($evaluation->id, $criteria->id, [
            'score' => 85,
            'notes' => 'First score',
        ]);
        
        // Try to create duplicate via service - should update existing instead
        $score2 = $service->scoreCriteria($evaluation->id, $criteria->id, [
            'score' => 90,
            'notes' => 'Updated score',
        ]);
        
        // Should be the same record (updated)
        $this->assertEquals($score1->id, $score2->id);
        $this->assertEquals(90, $score2->score);
        $this->assertEquals('Updated score', $score2->notes);
        
        // Should only have one record in database
        $this->assertEquals(1, EvaluationCriteriaScore::where([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
        ])->count());
    }
}