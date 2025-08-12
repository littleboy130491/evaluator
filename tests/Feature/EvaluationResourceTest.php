<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_evaluation_resource()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/evaluations');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_create_evaluation()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/evaluations/create');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_edit_evaluation()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        $evaluation = Evaluation::factory()->create();
        
        $response = $this->actingAs($admin)->get("/admin/evaluations/{$evaluation->id}/edit");
        
        $response->assertStatus(200);
    }

    public function test_evaluator_can_view_evaluation_resource()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/evaluations');
        
        $response->assertStatus(200);
    }

    public function test_evaluator_can_create_evaluation()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/evaluations/create');
        
        $response->assertStatus(200);
    }

    public function test_manager_can_view_evaluation_resource()
    {
        $this->seed();
        
        $manager = User::where('email', 'manager@example.com')->first();
        
        $response = $this->actingAs($manager)->get('/admin/evaluations');
        
        $response->assertStatus(200);
    }
}