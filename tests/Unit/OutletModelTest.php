<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_outlet(): void
    {
        $outlet = Outlet::factory()->create();

        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'name' => $outlet->name,
            'address' => $outlet->address,
        ]);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $outlet = Outlet::factory()->create();
        $outlet->delete();

        $this->assertSoftDeleted('outlets', ['id' => $outlet->id]);
    }

    /** @test */
    public function it_has_evaluations_relationship(): void
    {
        $outlet = Outlet::factory()->create();
        $evaluation = Evaluation::factory()->create(['outlet_id' => $outlet->id]);

        $this->assertTrue($outlet->evaluations->contains($evaluation));
        $this->assertInstanceOf(Evaluation::class, $outlet->evaluations->first());
    }

    /** @test */
    public function it_validates_required_fields(): void
    {
        // Attempt to create an outlet without required fields
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Outlet::create([
            // Missing 'name' and 'address'
            'phone_number' => '123-456-7890',
        ]);
    }
}