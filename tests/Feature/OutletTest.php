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
    public function admin_can_access_outlet_index_page(): void
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/outlets');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_outlet_create_page(): void
    {
        $this->seed();
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/outlets/create');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function outlet_model_can_be_created(): void
    {
        $outletData = [
            'name' => 'Test Outlet',
            'address' => '123 Test Street',
            'phone_number' => '555-1234',
            'manager_name' => 'Test Manager',
            'notes' => 'Test notes',
        ];

        $outlet = Outlet::create($outletData);

        $this->assertDatabaseHas('outlets', ['name' => 'Test Outlet']);
        $this->assertEquals('Test Outlet', $outlet->name);
    }

    /** @test */
    public function outlet_requires_name_and_address(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Outlet::create([
            'phone_number' => '555-1234',
            'manager_name' => 'Test Manager',
        ]);
    }

    /** @test */
    public function outlet_can_be_updated(): void
    {
        $outlet = Outlet::factory()->create();
        
        $outlet->update(['name' => 'Updated Outlet Name']);
        
        $this->assertDatabaseHas('outlets', ['id' => $outlet->id, 'name' => 'Updated Outlet Name']);
    }

    /** @test */
    public function outlet_can_be_soft_deleted(): void
    {
        $outlet = Outlet::factory()->create();
        
        $outlet->delete();
        
        $this->assertSoftDeleted($outlet);
    }
}