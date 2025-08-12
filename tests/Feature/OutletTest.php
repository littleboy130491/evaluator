<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_user_can_create_an_outlet(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $outletData = [
            'name' => 'Test Outlet',
            'address' => '123 Test Street',
            'phone_number' => '555-1234',
            'manager_name' => 'Test Manager',
            'notes' => 'Test notes',
        ];

        $response = $this->post('/outlets', $outletData);

        // This test will fail until we create the controller and routes
        // But it serves as a specification for what we want to implement
        $response->assertRedirect();
        $this->assertDatabaseHas('outlets', ['name' => 'Test Outlet']);
    }

    /** @test */
    public function outlet_requires_name_and_address(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/outlets', [
            'phone_number' => '555-1234',
            'manager_name' => 'Test Manager',
        ]);

        // This test will fail until we create the controller with validation
        // But it serves as a specification for what we want to implement
        $response->assertSessionHasErrors(['name', 'address']);
    }

    /** @test */
    public function authorized_user_can_update_an_outlet(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $outlet = Outlet::factory()->create();
        $updatedData = [
            'name' => 'Updated Outlet Name',
            'address' => $outlet->address,
            'phone_number' => $outlet->phone_number,
            'manager_name' => $outlet->manager_name,
        ];

        $response = $this->put("/outlets/{$outlet->id}", $updatedData);

        // This test will fail until we create the controller and routes
        // But it serves as a specification for what we want to implement
        $response->assertRedirect();
        $this->assertDatabaseHas('outlets', ['id' => $outlet->id, 'name' => 'Updated Outlet Name']);
    }

    /** @test */
    public function authorized_user_can_delete_an_outlet(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $outlet = Outlet::factory()->create();

        $response = $this->delete("/outlets/{$outlet->id}");

        // This test will fail until we create the controller and routes
        // But it serves as a specification for what we want to implement
        $response->assertRedirect();
        $this->assertSoftDeleted($outlet);
    }
}