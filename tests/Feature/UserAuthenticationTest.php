<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_filament_login_page(): void
    {
        $response = $this->get('/admin/login');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_login_to_filament(): void
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_user_can_logout_from_filament(): void
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $this->actingAs($admin);
        $this->assertAuthenticated();

        $response = $this->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_cannot_access_filament_admin(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertStatus(403);
    }
}