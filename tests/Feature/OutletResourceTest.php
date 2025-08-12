<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_outlet_resource()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/outlets');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_create_outlet()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/outlets/create');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_edit_outlet()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        $outlet = Outlet::factory()->create();
        
        $response = $this->actingAs($admin)->get("/admin/outlets/{$outlet->id}/edit");
        
        $response->assertStatus(200);
    }

    public function test_evaluator_can_view_outlet_resource()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/outlets');
        
        $response->assertStatus(200);
    }

    public function test_evaluator_cannot_create_outlet()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin/outlets/create');
        
        $response->assertStatus(403);
    }

    public function test_manager_can_view_outlet_resource()
    {
        $this->seed();
        
        $manager = User::where('email', 'manager@example.com')->first();
        
        $response = $this->actingAs($manager)->get('/admin/outlets');
        
        $response->assertStatus(200);
    }
}