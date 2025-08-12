<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_filament_panel()
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_evaluator_can_access_filament_panel()
    {
        $this->seed();
        
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        
        $response = $this->actingAs($evaluator)->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_manager_can_access_filament_panel()
    {
        $this->seed();
        
        $manager = User::where('email', 'manager@example.com')->first();
        
        $response = $this->actingAs($manager)->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_filament_panel()
    {
        $response = $this->get('/admin');
        
        $response->assertRedirect('/admin/login');
    }
}