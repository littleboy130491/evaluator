<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the outlets for the group area.
     */
    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class);
    }

    /**
     * Get the auditors assigned to this group area.
     */
    public function auditors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_area_user')
            ->wherePivot('role', 'auditor')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get all users assigned to this group area.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_area_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}