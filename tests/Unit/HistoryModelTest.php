<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use App\Models\History;
use App\Models\User;
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
}