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
            
            // Outlet management (Filament Shield format)
            'view_any_outlet',
            'view_outlet',
            'create_outlet',
            'update_outlet',
            'delete_outlet',
            'delete_any_outlet',
            'force_delete_outlet',
            'force_delete_any_outlet',
            'restore_outlet',
            'restore_any_outlet',
            'replicate_outlet',
            'reorder_outlet',
            
            // Evaluation criteria permissions (Filament Shield format)
            'view_any_evaluation::criteria',
            'view_evaluation::criteria',
            'create_evaluation::criteria',
            'update_evaluation::criteria',
            'delete_evaluation::criteria',
            'delete_any_evaluation::criteria',
            'force_delete_evaluation::criteria',
            'force_delete_any_evaluation::criteria',
            'restore_evaluation::criteria',
            'restore_any_evaluation::criteria',
            'replicate_evaluation::criteria',
            'reorder_evaluation::criteria',
            
            // Evaluation management (Filament Shield format)
            'view_any_evaluation',
            'view_evaluation',
            'create_evaluation',
            'update_evaluation',
            'delete_evaluation',
            'delete_any_evaluation',
            'force_delete_evaluation',
            'force_delete_any_evaluation',
            'restore_evaluation',
            'restore_any_evaluation',
            'replicate_evaluation',
            'reorder_evaluation',
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
            'create_evaluation',
            'update_evaluation',
            'view_evaluation',
            'view_any_evaluation',
            'view_outlet',
            'view_any_outlet',
            'view_evaluation::criteria',
            'view reports',
        ]);

        // Manager role
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view_evaluation',
            'view_any_evaluation',
            'update_evaluation',
            'approve evaluations',
            'reject evaluations',
            'view_outlet',
            'view_any_outlet',
            'view_evaluation::criteria',
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