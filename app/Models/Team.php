<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Users that belong to this team (many-to-many)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Team leaders
     */
    public function leaders(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'leader');
    }

    /**
     * Team members (non-leaders)
     */
    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    /**
     * User who created this team
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Projects assigned to this team
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Stages assigned to this team
     */
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * Tasks assigned to this team
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Check if a user is a leader of this team
     */
    public function isLeader(User $user): bool
    {
        return $this->users()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'leader')
            ->exists();
    }

    /**
     * Check if a user is a member of this team (any role)
     */
    public function isMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Add a user to this team
     */
    public function addMember(User $user, string $role = 'member'): void
    {
        if (!$this->isMember($user)) {
            $this->users()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    /**
     * Remove a user from this team
     */
    public function removeMember(User $user): void
    {
        $this->users()->detach($user->id);
    }

    /**
     * Promote a member to leader
     */
    public function promoteToLeader(User $user): void
    {
        if ($this->isMember($user)) {
            $this->users()->updateExistingPivot($user->id, ['role' => 'leader']);
        }
    }

    /**
     * Demote a leader to member
     */
    public function demoteToMember(User $user): void
    {
        if ($this->isMember($user)) {
            $this->users()->updateExistingPivot($user->id, ['role' => 'member']);
        }
    }

    /**
     * Get color badge class
     */
    public function getColorBadgeClass(): string
    {
        return match ($this->color) {
            'blue' => 'bg-blue-500/20 text-blue-300 ring-blue-500/30',
            'green' => 'bg-green-500/20 text-green-300 ring-green-500/30',
            'purple' => 'bg-purple-500/20 text-purple-300 ring-purple-500/30',
            'orange' => 'bg-orange-500/20 text-orange-300 ring-orange-500/30',
            'pink' => 'bg-pink-500/20 text-pink-300 ring-pink-500/30',
            'yellow' => 'bg-yellow-500/20 text-yellow-300 ring-yellow-500/30',
            default => 'bg-gray-500/20 text-gray-300 ring-gray-500/30',
        };
    }

    /**
     * Scope to only active teams
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
