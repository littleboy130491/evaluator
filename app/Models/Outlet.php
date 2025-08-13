<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'manager_name',
        'notes',
        'group_area_id',
    ];

    /**
     * Get the group area that owns the outlet.
     */
    public function groupArea(): BelongsTo
    {
        return $this->belongsTo(GroupArea::class);
    }

    /**
     * Get the evaluations for the outlet.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}