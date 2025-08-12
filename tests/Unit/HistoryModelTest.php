<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\EvaluationCriteriaScore;
use App\Models\History;
use App\Models\User;
use App\Services\EvaluationService;
use App\Services\HistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_history_entry(): void
    {
        $history = History::factory()->create();

        $this->assertDatabaseHas('histories', [
            'id' => $history->id,
            'historiable_type' => $history->historiable_type,
            'historiable_id' => $history->historiable_id,
            'user_id' => $history->user_id,
            'action' => $history->action,
        ]);
    }

    /** @test */
    public function it_has_user_relationship(): void
    {
        $user = User::factory()->create();
        $history = History::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $history->user->id);
        $this->assertInstanceOf(User::class, $history->user);
    }

    /** @test */
    public function it_has_morphable_relationship(): void
    {
        $evaluation = Evaluation::factory()->create();
        $history = History::factory()->create([
            'historiable_type' => Evaluation::class,
            'historiable_id' => $evaluation->id,
        ]);

        $this->assertEquals($evaluation->id, $history->historiable->id);
        $this->assertInstanceOf(Evaluation::class, $history->historiable);
    }

    /** @test */
    public function it_casts_json_attributes_correctly(): void
    {
        $oldValues = ['status' => 'draft'];
        $newValues = ['status' => 'submitted'];
        
        $history = History::factory()->create([
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        $this->assertIsArray($history->old_values);
        $this->assertIsArray($history->new_values);
        $this->assertEquals($oldValues, $history->old_values);
        $this->assertEquals($newValues, $history->new_values);
    }

    /** @test */
    public function it_creates_history_record_when_evaluation_is_created(): void
    {
        // Create a new evaluation
        $evaluation = Evaluation::factory()->create();

        // Check if a history record was created
        $this->assertDatabaseHas('histories', [
            'historiable_type' => Evaluation::class,
            'historiable_id' => $evaluation->id,
            'action' => 'created',
        ]);

        // Verify the new_values contains the evaluation attributes
        $history = History::where('historiable_id', $evaluation->id)
            ->where('action', 'created')
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals($evaluation->id, $history->new_values['id']);
    }

    /** @test */
    public function it_creates_history_record_when_evaluation_is_updated(): void
    {
        // Create a new evaluation
        $evaluation = Evaluation::factory()->create();

        // Clear existing history records
        History::query()->delete();

        // Update the evaluation
        $evaluation->update(['notes' => 'Updated notes']);

        // Check if a history record was created for the update
        $this->assertDatabaseHas('histories', [
            'historiable_type' => Evaluation::class,
            'historiable_id' => $evaluation->id,
            'action' => 'updated',
        ]);

        // Verify the old_values and new_values
        $history = History::where('historiable_id', $evaluation->id)
            ->where('action', 'updated')
            ->first();
            
        $this->assertNotNull($history);
        $this->assertArrayHasKey('notes', $history->old_values);
        $this->assertArrayHasKey('notes', $history->new_values);
        $this->assertEquals('Updated notes', $history->new_values['notes']);
    }

    /** @test */
    public function it_creates_history_record_when_evaluation_status_is_changed(): void
    {
        // Create dependencies
        $historyService = new HistoryService();
        $evaluationService = new EvaluationService($historyService);
        
        // Create a new evaluation
        $evaluation = Evaluation::factory()->create(['status' => 'draft']);

        // Clear existing history records
        History::query()->delete();

        // Change the status
        $evaluationService->changeStatus($evaluation->id, 'submitted');

        // Check if a history record was created for the status change
        $this->assertDatabaseHas('histories', [
            'historiable_type' => Evaluation::class,
            'historiable_id' => $evaluation->id,
            'action' => 'status_changed',
        ]);

        // Verify the old_values and new_values
        $history = History::where('historiable_id', $evaluation->id)
            ->where('action', 'status_changed')
            ->first();
            
        $this->assertNotNull($history);
        $this->assertEquals(['status' => 'draft'], $history->old_values);
        $this->assertEquals(['status' => 'submitted'], $history->new_values);
    }

    // Skipping this test for now due to database setup issues
    // /** @test */
    // public function it_creates_history_record_when_criteria_score_is_created(): void
    // {
    //     // This test will be implemented after fixing database issues
    // }
}