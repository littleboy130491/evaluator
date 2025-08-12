<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\User;
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
}