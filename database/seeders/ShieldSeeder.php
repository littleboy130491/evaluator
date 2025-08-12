<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[
            {
                "name":"super_admin",
                "guard_name":"web",
                "permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role"]
            },
            {
                "name":"admin",
                "guard_name":"web",
                "permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_user","view_any_user","create_user","update_user","delete_user","delete_any_user","view_outlet","view_any_outlet","create_outlet","update_outlet","delete_outlet","delete_any_outlet","view_evaluation","view_any_evaluation","create_evaluation","update_evaluation","delete_evaluation","delete_any_evaluation","view_criteria","view_any_criteria","create_criteria","update_criteria","delete_criteria","delete_any_criteria","view_criteria_score","view_any_criteria_score","create_criteria_score","update_criteria_score","delete_criteria_score","delete_any_criteria_score"]
            },
            {
                "name":"evaluator",
                "guard_name":"web",
                "permissions":["view_outlet","view_any_outlet","view_evaluation","view_any_evaluation","create_evaluation","update_evaluation","view_criteria","view_any_criteria","view_criteria_score","view_any_criteria_score","create_criteria_score","update_criteria_score"]
            },
            {
                "name":"manager",
                "guard_name":"web",
                "permissions":["view_outlet","view_any_outlet","view_evaluation","view_any_evaluation","update_evaluation","view_criteria","view_any_criteria","view_criteria_score","view_any_criteria_score"]
            }
        ]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);
        
        // Create default users for each role
        $this->createDefaultUsers();

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
    
    protected function createDefaultUsers(): void
    {
        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        
        // Create evaluator user
        $evaluator = User::updateOrCreate(
            ['email' => 'evaluator@example.com'],
            [
                'name' => 'Evaluator User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $evaluator->assignRole('evaluator');
        
        // Create manager user
        $manager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');
    }
}
