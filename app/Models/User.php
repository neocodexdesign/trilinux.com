<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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

    protected ?string $resolvedRoleCache = null;

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the tasks assigned to the user
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'responsible_id');
    }

    /**
     * Teams that this user belongs to (many-to-many)
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Teams where this user is a leader
     */
    public function leaderOf(): BelongsToMany
    {
        return $this->teams()->wherePivot('role', 'leader');
    }

    /**
     * Teams created by this user
     */
    public function createdTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'created_by');
    }

    /**
     * Check if user is a member of a specific team
     */
    public function isMemberOf(Team $team): bool
    {
        return $this->teams()->where('team_id', $team->id)->exists();
    }

    /**
     * Check if user is a leader of a specific team
     */
    public function isLeaderOf(Team $team): bool
    {
        return $this->teams()
            ->where('team_id', $team->id)
            ->wherePivot('role', 'leader')
            ->exists();
    }

    /**
     * Determines if this user acts as a platform superuser.
     */
    public function isSuperuser(): bool
    {
        return $this->role === 'superuser';
    }

    /**
     * Basic permission resolver based on predefined role capabilities.
     * This keeps policies working even without a dedicated ACL package.
     */
    public function hasPermissionTo(string $permission): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        $permissions = $this->permissionsForRole();

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        if (str_contains($permission, '.')) {
            [$group] = explode('.', $permission, 2);
            return in_array($group . '.*', $permissions, true);
        }

        return false;
    }

    protected function permissionsForRole(): array
    {
        return match ($this->role) {
            'admin' => [
                'task.*',
                'stage.*',
                'project.*',
            ],
            'operator' => [
                'task.view',
                'task.start',
                'task.pause',
                'task.resume',
                'task.complete',
                'stage.view',
                'project.view',
            ],
            'client' => [
                'task.view',
            ],
            default => [],
        };
    }

    /**
     * Provide access to the role attribute even when it's stored on the team_user pivot.
     */
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->resolveRoleFromTeams(),
            set: fn ($value) => $value
        );
    }

    protected function resolveRoleFromTeams(): string
    {
        if ($this->resolvedRoleCache !== null) {
            return $this->resolvedRoleCache;
        }

        $role = DB::table('team_user')
            ->where('user_id', $this->id)
            ->orderByDesc('joined_at')
            ->value('role');

        return $this->resolvedRoleCache = $role ?? 'client';
    }
}
