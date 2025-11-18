<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Template extends Model implements Auditable
{
    use HasFactory, LogsActivity, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'structure',
        'default_duration_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'structure', 'default_duration_days', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createProject(array $projectData): Project
    {
        $project = Project::create([
            'tenant_id' => $this->tenant_id,
            'name' => $projectData['name'],
            'description' => $projectData['description'] ?? $this->description,
            'status' => 'planned',
            'expected_start_at' => $projectData['expected_start_at'] ?? null,
            'expected_end_at' => $projectData['expected_end_at'] ?? ($projectData['expected_start_at'] ? 
                now()->parse($projectData['expected_start_at'])->addDays($this->default_duration_days ?? 30) : null),
            'created_by' => auth()->id(),
        ]);

        if (!empty($this->structure['stages'])) {
            foreach ($this->structure['stages'] as $stageIndex => $stageData) {
                $stage = Stage::create([
                    'tenant_id' => $this->tenant_id,
                    'project_id' => $project->id,
                    'name' => $stageData['name'],
                    'description' => $stageData['description'] ?? null,
                    'status' => 'planned',
                    'order' => $stageIndex + 1,
                    'expected_start_at' => isset($stageData['days_offset']) && $project->expected_start_at ? 
                        $project->expected_start_at->addDays($stageData['days_offset']) : null,
                    'expected_end_at' => isset($stageData['duration_days']) && $project->expected_start_at ? 
                        $project->expected_start_at->addDays(($stageData['days_offset'] ?? 0) + ($stageData['duration_days'] ?? 7)) : null,
                ]);

                if (!empty($stageData['tasks'])) {
                    foreach ($stageData['tasks'] as $taskIndex => $taskData) {
                        Task::create([
                            'tenant_id' => $this->tenant_id,
                            'stage_id' => $stage->id,
                            'name' => $taskData['name'],
                            'description' => $taskData['description'] ?? null,
                            'status' => 'planned',
                            'order' => $taskIndex + 1,
                            'estimated_hours' => $taskData['estimated_hours'] ?? null,
                        ]);
                    }
                }
            }
        }

        return $project;
    }
}