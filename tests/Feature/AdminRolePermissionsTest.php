<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $adminPermissions = [
        'manage users',
        'create users',
        'edit users',
        'delete users',
        'view users',
        'manage roles',
        'create roles',
        'edit roles',
        'delete roles',
        'view roles',
        'manage outlets',
        'create outlets',
        'edit outlets',
        'delete outlets',
        'view outlets',
        'manage criteria',
        'create criteria',
        'edit criteria',
        'delete criteria',
        'view criteria',
        'manage evaluations',
        'create evaluations',
        'edit evaluations',
        'delete evaluations',
        'view evaluations',
        'approve evaluations',
        'reject evaluations',
        'view reports',
        'create reports',
        'view history',
    ];

    public function test_admin_role_has_all_permissions(): void
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);

        // Create all permissions
        foreach ($this->adminPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($this->adminPermissions);

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Assert admin has all permissions
        foreach ($this->adminPermissions as $permissionName) {
            $this->assertTrue($admin->hasPermissionTo($permissionName));
        }
    }

    public function test_admin_can_manage_users(): void
    {
        // Create admin role with user management permissions
        $adminRole = Role::create(['name' => 'admin']);
        
        $userManagementPermissions = [
            'manage users',
            'create users',
            'edit users',
            'delete users',
            'view users'
        ];

        foreach ($userManagementPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        $adminRole->givePermissionTo($userManagementPermissions);

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Assert admin has user management permissions
        foreach ($userManagementPermissions as $permissionName) {
            $this->assertTrue($admin->hasPermissionTo($permissionName));
        }
    }
}