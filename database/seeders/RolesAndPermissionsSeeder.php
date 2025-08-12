<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'manage users',
            'create users',
            'edit users',
            'delete users',
            'view users',
            
            // Role management
            'manage roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view roles',
            
            // Outlet management
            'manage outlets',
            'create outlets',
            'edit outlets',
            'delete outlets',
            'view outlets',
            
            // Criteria management
            'manage criteria',
            'create criteria',
            'edit criteria',
            'delete criteria',
            'view criteria',
            
            // Evaluation management
            'manage evaluations',
            'create evaluations',
            'edit evaluations',
            'delete evaluations',
            'view evaluations',
            'approve evaluations',
            'reject evaluations',
            
            // Report management
            'view reports',
            'create reports',
            
            // History management
            'view history',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Evaluator role
        $evaluatorRole = Role::create(['name' => 'evaluator']);
        $evaluatorRole->givePermissionTo([
            'create evaluations',
            'edit evaluations',
            'view evaluations',
            'view outlets',
            'view criteria',
            'view reports',
        ]);

        // Manager role
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view evaluations',
            'approve evaluations',
            'reject evaluations',
            'view outlets',
            'view criteria',
            'view reports',
            'create reports',
            'view history',
        ]);

        // Create admin user
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole($adminRole);
        } else {
            $admin = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);
            $admin->assignRole($adminRole);
        }

        // Create evaluator user
        $evaluator = User::factory()->create([
            'name' => 'Evaluator User',
            'email' => 'evaluator@example.com',
        ]);
        $evaluator->assignRole($evaluatorRole);

        // Create manager user
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
        ]);
        $manager->assignRole($managerRole);
    }
}