<?php

namespace Tests\Feature;

use App\Models\EvaluationCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationCriteriaResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_evaluation_criteria_resource()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/evaluation-criterias');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_create_evaluation_criteria()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/evaluation-criterias/create');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_edit_evaluation_criteria()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        $criteria = EvaluationCriteria::factory()->create();
        
        $response = $this->actingAs($admin)->get("/admin/evaluation-criterias/{$criteria->id}/edit");
        
        $response->assertStatus(200);
    }

    public function test_evaluator_can_view_evaluation_criteria_resource()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/evaluation-criterias');
        
        $response->assertStatus(403);
    }

    public function test_evaluator_cannot_create_evaluation_criteria()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/evaluation-criterias/create');
        
        $response->assertStatus(403);
    }

    public function test_manager_can_view_evaluation_criteria_resource()
    {
        $this->seed();
        
        $manager = User::where('email', 'manager@example.com')->first();
        
        $response = $this->actingAs($manager)->get('/admin/evaluation-criterias');
        
        $response->assertStatus(403);
    }
}