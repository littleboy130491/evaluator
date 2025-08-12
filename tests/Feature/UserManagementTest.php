<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);
        
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_admin_can_edit_user(): void
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);
        
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Create user to edit
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $this->actingAs($admin);

        $response = $this->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(302); // Redirect after update
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);
        
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Create user to delete
        $user = User::factory()->create();

        $this->actingAs($admin);

        $response = $this->delete("/users/{$user->id}");

        $response->assertStatus(302); // Redirect after deletion
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_can_assign_role_to_user(): void
    {
        // Create admin and evaluator roles
        $adminRole = Role::create(['name' => 'admin']);
        $evaluatorRole = Role::create(['name' => 'evaluator']);
        
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Create user to assign role
        $user = User::factory()->create();

        $this->actingAs($admin);

        $response = $this->post("/users/{$user->id}/roles", [
            'roles' => ['evaluator'],
        ]);

        $response->assertStatus(302); // Redirect after role assignment
        $this->assertTrue($user->fresh()->hasRole('evaluator'));
    }
}