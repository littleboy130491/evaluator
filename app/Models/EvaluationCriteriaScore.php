<?php

namespace App\Models;

use App\Traits\HasHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationCriteriaScore extends Model
{
    use HasFactory, HasHistory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'evaluation_id',
        'evaluation_criteria_id',
        'score',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'evaluation_id' => 'required|exists:evaluations,id',
            'evaluation_criteria_id' => 'required|exists:evaluation_criteria,id',
            'score' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get validation rules with unique constraint for create
     */
    public static function createRules(): array
    {
        return array_merge(self::rules(), [
            'evaluation_criteria_id' => 'required|exists:evaluation_criteria,id|unique:evaluation_criteria_scores,evaluation_criteria_id,NULL,id,evaluation_id',
        ]);
    }

    /**
     * Get validation rules with unique constraint for update
     */
    public static function updateRules($id, $evaluationId): array
    {
        return array_merge(self::rules(), [
            'evaluation_criteria_id' => "required|exists:evaluation_criteria,id|unique:evaluation_criteria_scores,evaluation_criteria_id,{$id},id,evaluation_id,{$evaluationId}",
        ]);
    }

    /**
     * Get the evaluation that owns the score.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Get the criteria that owns the score.
     */
    public function criteria(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriteria::class, 'evaluation_criteria_id');
    }
}