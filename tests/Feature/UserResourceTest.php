<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_user_resource()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        
        $this->actingAs($admin);
        
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertSuccessful();
    }

    public function test_admin_can_create_user()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        
        $this->actingAs($admin);
        
        Livewire::test(UserResource\Pages\CreateUser::class)
            ->assertSuccessful();
    }

    public function test_admin_can_edit_user()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'evaluator@example.com')->first();
        
        $this->assertNotNull($admin, 'Admin user should exist after seeding');
        $this->assertNotNull($user, 'Evaluator user should exist after seeding');
        
        $this->actingAs($admin);
        
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $user->id])
            ->assertSuccessful();
    }

    public function test_evaluator_cannot_access_user_resource()
    {
        $evaluator = User::where('email', 'evaluator@example.com')->first();
        $this->assertNotNull($evaluator, 'Evaluator user should exist after seeding');
        
        $this->actingAs($evaluator);
        
        $response = $this->get('/admin/users');
        $response->assertForbidden();
    }

    public function test_manager_cannot_access_user_resource()
    {
        $manager = User::where('email', 'manager@example.com')->first();
        $this->assertNotNull($manager, 'Manager user should exist after seeding');
        
        $this->actingAs($manager);
        
        $response = $this->get('/admin/users');
        $response->assertForbidden();
    }
}