<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the evaluations created by the user.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the evaluations approved by the user.
     */
    public function approvedEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'approved_by');
    }

    /**
     * Get the reports created by the user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the history entries created by the user.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }

    /**
     * Get the group areas assigned to this user.
     */
    public function groupAreas(): BelongsToMany
    {
        return $this->belongsToMany(GroupArea::class, 'group_area_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the group areas where this user is an auditor.
     */
    public function auditorGroupAreas(): BelongsToMany
    {
        return $this->belongsToMany(GroupArea::class, 'group_area_user')
            ->wherePivot('role', 'auditor')
            ->withTimestamps();
    }

    /**
     * Check if user can evaluate outlets in a specific group area.
     */
    public function canEvaluateInGroupArea(GroupArea $groupArea): bool
    {
        // Super admin and admin can evaluate in any group area
        if ($this->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // Check if user is assigned as auditor to this group area
        return $this->auditorGroupAreas()->where('group_areas.id', $groupArea->id)->exists();
    }

    /**
     * Get outlets that this user can evaluate.
     */
    public function getEvaluableOutlets()
    {
        // Super admin and admin can evaluate all outlets
        if ($this->hasAnyRole(['super_admin', 'admin'])) {
            return Outlet::all();
        }

        // Get outlets from group areas where user is an auditor
        $groupAreaIds = $this->auditorGroupAreas()->pluck('group_areas.id');
        return Outlet::whereIn('group_area_id', $groupAreaIds)->get();
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow access for users with admin, super_admin, manager, or evaluator roles
        return $this->hasAnyRole(['super_admin', 'admin', 'manager', 'evaluator']);
    }
}
