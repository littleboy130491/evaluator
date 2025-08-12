<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EvaluatorRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $evaluatorPermissions = [
        'create evaluations',
        'edit evaluations',
        'view evaluations',
        'view outlets',
        'view criteria',
        'view reports',
    ];

    public function test_evaluator_role_has_appropriate_permissions(): void
    {
        // Create evaluator role
        $evaluatorRole = Role::create(['name' => 'evaluator']);

        // Create permissions
        foreach ($this->evaluatorPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Assign permissions to evaluator role
        $evaluatorRole->givePermissionTo($this->evaluatorPermissions);

        // Create evaluator user
        $evaluator = User::factory()->create();
        $evaluator->assignRole($evaluatorRole);

        // Assert evaluator has appropriate permissions
        foreach ($this->evaluatorPermissions as $permissionName) {
            $this->assertTrue($evaluator->hasPermissionTo($permissionName));
        }
    }

    public function test_evaluator_cannot_approve_evaluations(): void
    {
        // Create evaluator role
        $evaluatorRole = Role::create(['name' => 'evaluator']);

        // Create permissions including evaluator permissions
        foreach ($this->evaluatorPermissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }
        
        // Create approve permission
        Permission::create(['name' => 'approve evaluations']);

        // Assign only evaluator permissions to role
        $evaluatorRole->givePermissionTo($this->evaluatorPermissions);

        // Create evaluator user
        $evaluator = User::factory()->create();
        $evaluator->assignRole($evaluatorRole);

        // Assert evaluator cannot approve evaluations
        $this->assertFalse($evaluator->hasPermissionTo('approve evaluations'));
    }
}