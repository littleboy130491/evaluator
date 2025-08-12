<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_assigned_role(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a role
        $role = Role::create(['name' => 'admin']);

        // Assign role to user
        $user->assignRole($role);

        // Assert user has role
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_can_be_assigned_multiple_roles(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $evaluatorRole = Role::create(['name' => 'evaluator']);

        // Assign roles to user
        $user->assignRole($adminRole);
        $user->assignRole($evaluatorRole);

        // Assert user has roles
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('evaluator'));
    }

    public function test_user_can_have_permission(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a permission
        $permission = Permission::create(['name' => 'edit evaluations']);

        // Assign permission to user
        $user->givePermissionTo($permission);

        // Assert user has permission
        $this->assertTrue($user->hasPermissionTo('edit evaluations'));
    }

    public function test_role_can_have_permission(): void
    {
        // Create a role
        $role = Role::create(['name' => 'evaluator']);

        // Create a permission
        $permission = Permission::create(['name' => 'create evaluations']);

        // Assign permission to role
        $role->givePermissionTo($permission);

        // Create a user and assign role
        $user = User::factory()->create();
        $user->assignRole($role);

        // Assert user has permission via role
        $this->assertTrue($user->hasPermissionTo('create evaluations'));
    }
}