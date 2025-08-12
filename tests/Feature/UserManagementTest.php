<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Livewire\Livewire;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        
        $this->actingAs($admin);

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password',
                'roles' => [1], // Admin role ID
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);
    }

    public function test_admin_can_edit_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'evaluator@example.com')->first();
        
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        $this->assertNotNull($user, 'Evaluator user should exist after seeding');

        $this->actingAs($admin);

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $user->id])
            ->fillForm([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'evaluator@example.com')->first();
        
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        $this->assertNotNull($user, 'Evaluator user should exist after seeding');

        $this->actingAs($admin);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->callTableAction('delete', $user->id);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_can_assign_role_to_user(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'evaluator@example.com')->first();
        $managerRole = Role::where('name', 'manager')->first();
        
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        $this->assertNotNull($user, 'Evaluator user should exist after seeding');
        $this->assertNotNull($managerRole, 'Manager role should exist after seeding');

        $this->actingAs($admin);

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $user->id])
            ->fillForm([
                'roles' => [$managerRole->id], // Assign manager role
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($user->fresh()->hasRole('manager'));
    }
}