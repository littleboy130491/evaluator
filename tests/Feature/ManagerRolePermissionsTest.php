<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ManagerRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $managerPermissions = [
        'view evaluations',
        'approve evaluations',
        'reject evaluations',
        'view outlets',
        'view criteria',
        'view reports',
        'create reports',
        'view history',
    ];

    public function test_manager_role_has_appropriate_permissions(): void
    {
        // Create manager role
        $managerRole = Role::create(['name' => 'manager']);

        // Create permissions
        foreach ($this->managerPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Assign permissions to manager role
        $managerRole->givePermissionTo($this->managerPermissions);

        // Create manager user
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        // Assert manager has appropriate permissions
        foreach ($this->managerPermissions as $permissionName) {
            $this->assertTrue($manager->hasPermissionTo($permissionName));
        }
    }

    public function test_manager_can_approve_and_reject_evaluations(): void
    {
        // Create manager role
        $managerRole = Role::create(['name' => 'manager']);

        // Create approve and reject permissions
        Permission::create(['name' => 'approve evaluations']);
        Permission::create(['name' => 'reject evaluations']);

        // Assign permissions to manager role
        $managerRole->givePermissionTo(['approve evaluations', 'reject evaluations']);

        // Create manager user
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        // Assert manager can approve and reject evaluations
        $this->assertTrue($manager->hasPermissionTo('approve evaluations'));
        $this->assertTrue($manager->hasPermissionTo('reject evaluations'));
    }

    public function test_manager_cannot_create_evaluations(): void
    {
        // Create manager role
        $managerRole = Role::create(['name' => 'manager']);

        // Create permissions including manager permissions
        foreach ($this->managerPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }
        
        // Create create evaluation permission
        Permission::create(['name' => 'create evaluations']);

        // Assign only manager permissions to role
        $managerRole->givePermissionTo($this->managerPermissions);

        // Create manager user
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        // Assert manager cannot create evaluations
        $this->assertFalse($manager->hasPermissionTo('create evaluations'));
    }
}