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