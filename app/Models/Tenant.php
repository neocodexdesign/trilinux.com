<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Spatie\Activitylog\Traits\LogsActivity;
// use Spatie\Activitylog\LogOptions;
// use OwenIt\Auditing\Contracts\Auditable;
// use OwenIt\Auditing\Auditable as AuditableTrait;


use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory; // LogsActivity, AuditableTrait;
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // (opcional) fixar tenancy_db_name no save:
    protected static function booted(): void
    {
        static::saving(function (self $tenant) {
            $tenant->setAttribute('tenancy_db_name', $tenant->getDatabaseName());
        });
    }

    public function getDatabaseName(): string
    {
        // A) baseado no ID:
        return "tenant_{$this->getTenantKey()}_todo";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'tenant_user', 'tenant_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }


    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}