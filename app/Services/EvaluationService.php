<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    /**
     * @var HistoryService
     */
    protected $historyService;

    /**
     * Constructor
     */
    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }
    /**
     * Create a new evaluation
     *
     * @param int $userId
     * @param array $data
     * @return Evaluation
     */
    public function createEvaluation(int $userId, array $data): Evaluation
    {
        return Evaluation::create([
            'user_id' => $userId,
            'outlet_id' => $data['outlet_id'],
            'evaluation_date' => $data['evaluation_date'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Update an existing evaluation
     *
     * @param int $evaluationId
     * @param array $data
     * @return Evaluation
     */
    public function updateEvaluation(int $evaluationId, array $data): Evaluation
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        
        $evaluation->update([
            'outlet_id' => $data['outlet_id'] ?? $evaluation->outlet_id,
            'evaluation_date' => $data['evaluation_date'] ?? $evaluation->evaluation_date,
            'notes' => $data['notes'] ?? $evaluation->notes,
        ]);
        
        return $evaluation->fresh();
    }

    /**
     * Score a criteria for an evaluation
     *
     * @param int $evaluationId
     * @param int $criteriaId
     * @param array $data
     * @return EvaluationCriteriaScore
     */
    public function scoreCriteria(int $evaluationId, int $criteriaId, array $data): EvaluationCriteriaScore
    {
        // Validate score against max_score
        $criteria = EvaluationCriteria::findOrFail($criteriaId);
        $score = min($data['score'], $criteria->max_score);
        
        // Check if score already exists
        $criteriaScore = EvaluationCriteriaScore::where([
            'evaluation_id' => $evaluationId,
            'evaluation_criteria_id' => $criteriaId,
        ])->first();
        
        if ($criteriaScore) {
            $criteriaScore->update([
                'score' => $score,
                'notes' => $data['notes'] ?? $criteriaScore->notes,
            ]);
            return $criteriaScore->fresh();
        }
        
        // Create new score
        return EvaluationCriteriaScore::create([
            'evaluation_id' => $evaluationId,
            'evaluation_criteria_id' => $criteriaId,
            'score' => $score,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Calculate the total score for an evaluation
     *
     * @param int $evaluationId
     * @return int
     */
    public function calculateTotalScore(int $evaluationId): int
    {
        $evaluation = Evaluation::with('criteriaScores.criteria')->findOrFail($evaluationId);
        
        $totalScore = 0;
        $totalMaxScore = 0;
        
        foreach ($evaluation->criteriaScores as $criteriaScore) {
            $totalScore += $criteriaScore->score;
            $totalMaxScore += $criteriaScore->criteria->max_score;
        }
        
        if ($totalMaxScore === 0) {
            return 0;
        }
        
        // Calculate percentage and round to nearest integer
        return (int) round(($totalScore / $totalMaxScore) * 100);
    }

    /**
     * Change the status of an evaluation
     *
     * @param int $evaluationId
     * @param string $status
     * @return Evaluation
     */
    public function changeStatus(int $evaluationId, string $status): Evaluation
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $oldStatus = $evaluation->status;
        
        // Validate status is one of the allowed values
        if (!in_array($status, ['draft', 'pending', 'submitted', 'completed', 'approved', 'rejected'])) {
            throw new \InvalidArgumentException("Invalid status value: {$status}");
        }
        
        $evaluation->update([
            'status' => $status,
        ]);
        
        // Record status change specifically
        $this->historyService->recordHistory(
            $evaluation,
            'status_changed',
            ['status' => $oldStatus],
            ['status' => $status]
        );
        
        return $evaluation->fresh();
    }

    /**
     * Approve an evaluation
     *
     * @param int $evaluationId
     * @param int $approverId
     * @return Evaluation
     */
    public function approveEvaluation(int $evaluationId, int $approverId): Evaluation
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $oldValues = [
            'status' => $evaluation->status,
            'approved_by' => $evaluation->approved_by,
            'approved_at' => $evaluation->approved_at,
        ];
        
        $newValues = [
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ];
        
        $evaluation->update($newValues);
        
        // Record approval action
        $this->historyService->recordHistory(
            $evaluation,
            'approved',
            $oldValues,
            $newValues
        );
        
        return $evaluation->fresh();
    }

    /**
     * Reject an evaluation
     *
     * @param int $evaluationId
     * @param int $approverId
     * @return Evaluation
     */
    public function rejectEvaluation(int $evaluationId, int $approverId): Evaluation
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $oldValues = [
            'status' => $evaluation->status,
            'approved_by' => $evaluation->approved_by,
            'approved_at' => $evaluation->approved_at,
        ];
        
        $newValues = [
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ];
        
        $evaluation->update($newValues);
        
        // Record rejection action
        $this->historyService->recordHistory(
            $evaluation,
            'rejected',
            $oldValues,
            $newValues
        );
        
        return $evaluation->fresh();
    }

    /**
     * List evaluations with optional filters
     *
     * @param array $filters
     * @return Collection
     */
    public function listEvaluations(array $filters = []): Collection
    {
        $query = Evaluation::query();
        
        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['from_date'])) {
            $query->where('evaluation_date', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date'])) {
            $query->where('evaluation_date', '<=', $filters['to_date']);
        }
        
        return $query->get();
    }
}