<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_report(): void
    {
        $report = Report::factory()->create();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'name' => $report->name,
            'type' => $report->type,
            'user_id' => $report->user_id,
        ]);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $report = Report::factory()->create();
        $report->delete();

        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    /** @test */
    public function it_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $report = Report::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $report->user->id);
        $this->assertInstanceOf(User::class, $report->user);
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $parameters = ['start_date' => '2023-01-01', 'end_date' => '2023-01-31'];
        $report = Report::factory()->create([
            'parameters' => $parameters,
            'generated_at' => '2023-01-15 10:00:00',
        ]);

        $this->assertIsArray($report->parameters);
        $this->assertEquals($parameters, $report->parameters);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $report->generated_at);
    }
}