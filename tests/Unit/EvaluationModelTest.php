<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\EvaluationCriteriaScore;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_evaluation(): void
    {
        $evaluation = Evaluation::factory()->create();

        $this->assertDatabaseHas('evaluations', [
            'id' => $evaluation->id,
            'user_id' => $evaluation->user_id,
            'outlet_id' => $evaluation->outlet_id,
            'status' => $evaluation->status,
        ]);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $evaluation = Evaluation::factory()->create();
        $evaluation->delete();

        $this->assertSoftDeleted('evaluations', ['id' => $evaluation->id]);
    }

    /** @test */
    public function it_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $evaluation = Evaluation::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $evaluation->user->id);
        $this->assertInstanceOf(User::class, $evaluation->user);
    }

    /** @test */
    public function it_has_outlet_relationship(): void
    {
        $outlet = Outlet::factory()->create();
        $evaluation = Evaluation::factory()->create(['outlet_id' => $outlet->id]);

        $this->assertEquals($outlet->id, $evaluation->outlet->id);
        $this->assertInstanceOf(Outlet::class, $evaluation->outlet);
    }

    /** @test */
    public function it_has_approver_relationship(): void
    {
        $approver = User::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $this->assertEquals($approver->id, $evaluation->approver->id);
        $this->assertInstanceOf(User::class, $evaluation->approver);
    }

    /** @test */
    public function it_has_criteria_scores_relationship(): void
    {
        $evaluation = Evaluation::factory()->create();
        $score = EvaluationCriteriaScore::factory()->create(['evaluation_id' => $evaluation->id]);

        $this->assertTrue($evaluation->criteriaScores->contains($score));
        $this->assertInstanceOf(EvaluationCriteriaScore::class, $evaluation->criteriaScores->first());
    }

    /** @test */
    public function it_casts_attributes_correctly(): void
    {
        $evaluation = Evaluation::factory()->create([
            'evaluation_date' => '2023-01-01',
            'approved_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $evaluation->evaluation_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $evaluation->approved_at);
    }
}