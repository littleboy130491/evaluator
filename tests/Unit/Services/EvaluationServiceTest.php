<?php

namespace Tests\Unit\Services;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use App\Models\Outlet;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationServiceTest extends TestCase
{
    use RefreshDatabase;

    private EvaluationService $evaluationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationService = new EvaluationService();
    }

    /** @test */
    public function it_can_create_an_evaluation(): void
    {
        $user = User::factory()->create();
        $outlet = Outlet::factory()->create();
        
        $data = [
            'outlet_id' => $outlet->id,
            'evaluation_date' => now()->format('Y-m-d'),
            'notes' => 'Test evaluation notes',
        ];

        $evaluation = $this->evaluationService->createEvaluation($user->id, $data);

        $this->assertInstanceOf(Evaluation::class, $evaluation);
        $this->assertEquals($user->id, $evaluation->user_id);
        $this->assertEquals($outlet->id, $evaluation->outlet_id);
        $this->assertEquals('pending', $evaluation->status);
        $this->assertEquals($data['notes'], $evaluation->notes);
    }

    /** @test */
    public function it_can_update_an_evaluation(): void
    {
        $evaluation = Evaluation::factory()->create([
            'status' => 'pending'
        ]);
        
        $newData = [
            'outlet_id' => Outlet::factory()->create()->id,
            'evaluation_date' => now()->addDay()->format('Y-m-d'),
            'notes' => 'Updated notes',
        ];

        $updatedEvaluation = $this->evaluationService->updateEvaluation($evaluation->id, $newData);

        $this->assertEquals($newData['outlet_id'], $updatedEvaluation->outlet_id);
        $this->assertEquals($newData['evaluation_date'], $updatedEvaluation->evaluation_date->format('Y-m-d'));
        $this->assertEquals($newData['notes'], $updatedEvaluation->notes);
    }

    /** @test */
    public function it_can_score_criteria(): void
    {
        $evaluation = Evaluation::factory()->create();
        $criteria = EvaluationCriteria::factory()->create(['max_score' => 10]);
        
        $scoreData = [
            'score' => 8,
            'notes' => 'Good performance',
        ];

        $criteriaScore = $this->evaluationService->scoreCriteria($evaluation->id, $criteria->id, $scoreData);

        $this->assertInstanceOf(EvaluationCriteriaScore::class, $criteriaScore);
        $this->assertEquals($evaluation->id, $criteriaScore->evaluation_id);
        $this->assertEquals($criteria->id, $criteriaScore->evaluation_criteria_id);
        $this->assertEquals($scoreData['score'], $criteriaScore->score);
        $this->assertEquals($scoreData['notes'], $criteriaScore->notes);
    }

    /** @test */
    public function it_can_calculate_total_score(): void
    {
        $evaluation = Evaluation::factory()->create();
        
        // Create criteria with different max scores
        $criteria1 = EvaluationCriteria::factory()->create(['max_score' => 10]);
        $criteria2 = EvaluationCriteria::factory()->create(['max_score' => 5]);
        $criteria3 = EvaluationCriteria::factory()->create(['max_score' => 15]);
        
        // Score the criteria
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria1->id,
            'score' => 8, // 8/10 = 80%
        ]);
        
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria2->id,
            'score' => 4, // 4/5 = 80%
        ]);
        
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria3->id,
            'score' => 12, // 12/15 = 80%
        ]);
        
        // Calculate total score
        $totalScore = $this->evaluationService->calculateTotalScore($evaluation->id);
        
        // Expected: (8 + 4 + 12) / (10 + 5 + 15) = 24 / 30 = 0.8 = 80%
        $this->assertEquals(80, $totalScore);
    }

    /** @test */
    public function it_can_change_evaluation_status(): void
    {
        $evaluation = Evaluation::factory()->create(['status' => 'pending']);
        $manager = User::factory()->create();
        
        // Change to completed
        $completedEvaluation = $this->evaluationService->changeStatus($evaluation->id, 'completed');
        $this->assertEquals('completed', $completedEvaluation->status);
        
        // Change to approved
        $approvedEvaluation = $this->evaluationService->approveEvaluation($evaluation->id, $manager->id);
        $this->assertEquals('approved', $approvedEvaluation->status);
        $this->assertEquals($manager->id, $approvedEvaluation->approved_by);
        $this->assertNotNull($approvedEvaluation->approved_at);
        
        // Create a new evaluation for rejection test
        $evaluationForRejection = Evaluation::factory()->create(['status' => 'completed']);
        
        // Change to rejected
        $rejectedEvaluation = $this->evaluationService->rejectEvaluation($evaluationForRejection->id, $manager->id);
        $this->assertEquals('rejected', $rejectedEvaluation->status);
    }

    /** @test */
    public function it_can_list_evaluations_with_filters(): void
    {
        // Create test data
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $outlet1 = Outlet::factory()->create();
        $outlet2 = Outlet::factory()->create();
        
        // Create evaluations with different statuses
        Evaluation::factory()->count(3)->create([
            'user_id' => $user1->id,
            'outlet_id' => $outlet1->id,
            'status' => 'pending',
        ]);
        
        Evaluation::factory()->count(2)->create([
            'user_id' => $user1->id,
            'outlet_id' => $outlet2->id,
            'status' => 'completed',
        ]);
        
        Evaluation::factory()->count(4)->create([
            'user_id' => $user2->id,
            'outlet_id' => $outlet1->id,
            'status' => 'approved',
        ]);
        
        // Test filtering by user
        $userEvaluations = $this->evaluationService->listEvaluations(['user_id' => $user1->id]);
        $this->assertCount(5, $userEvaluations);
        
        // Test filtering by outlet
        $outletEvaluations = $this->evaluationService->listEvaluations(['outlet_id' => $outlet1->id]);
        $this->assertCount(7, $outletEvaluations);
        
        // Test filtering by status
        $pendingEvaluations = $this->evaluationService->listEvaluations(['status' => 'pending']);
        $this->assertCount(3, $pendingEvaluations);
        
        // Test combined filters
        $combinedFilters = $this->evaluationService->listEvaluations([
            'user_id' => $user1->id,
            'outlet_id' => $outlet1->id,
            'status' => 'pending',
        ]);
        $this->assertCount(3, $combinedFilters);
    }
}