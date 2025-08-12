<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\History;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_has_evaluations_relationship(): void
    {
        $user = User::factory()->create();
        $evaluation = Evaluation::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->evaluations->contains($evaluation));
        $this->assertInstanceOf(Evaluation::class, $user->evaluations->first());
    }

    /** @test */
    public function it_has_approved_evaluations_relationship(): void
    {
        $user = User::factory()->create();
        $evaluation = Evaluation::factory()->create(['approved_by' => $user->id]);

        $this->assertTrue($user->approvedEvaluations->contains($evaluation));
        $this->assertInstanceOf(Evaluation::class, $user->approvedEvaluations->first());
    }

    /** @test */
    public function it_has_reports_relationship(): void
    {
        $user = User::factory()->create();
        $report = Report::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->reports->contains($report));
        $this->assertInstanceOf(Report::class, $user->reports->first());
    }

    /** @test */
    public function it_has_histories_relationship(): void
    {
        $user = User::factory()->create();
        $history = History::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->histories->contains($history));
        $this->assertInstanceOf(History::class, $user->histories->first());
    }
}