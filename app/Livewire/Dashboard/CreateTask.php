<?php

namespace App\Livewire\Dashboard;

use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateTask extends Component
{
    use AuthorizesRequests;

    public ?int $projectId = null;
    public ?int $stageId = null;
    public ?int $teamId = null;
    public ?int $responsibleId = null;
    public ?int $dependentTaskId = null;

    public string $name = '';
    public ?string $description = null;
    public ?string $expectedStartAt = null;
    public ?string $expectedEndAt = null;
    public ?string $estimatedHours = null;

    public array $projectOptions = [];
    public array $stageOptions = [];
    public array $teamOptions = [];
    public array $responsibleOptions = [];
    public array $dependencyOptions = [];

    public function mount(): void
    {
        $user = Auth::user();
        Log::info('CreateTask mount attempt', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->role,
            'can_create' => $user ? $user->can('create', Task::class) : false,
        ]);

        $this->authorize('create', Task::class);
        $this->responsibleId = Auth::id();
        $this->loadSelectOptions();
    }

    public function updatedProjectId($value): void
    {
        // Limpar campos dependentes
        $this->stageId = null;
        $this->stageOptions = [];
        $this->dependentTaskId = null;
        $this->dependencyOptions = [];

        if (!$value) {
            return;
        }

        // Carregar estágios do projeto selecionado
        $stages = Stage::query()
            ->where('project_id', $value)
            ->orderBy('name')
            ->get();

        $this->stageOptions = $stages
            ->map(fn ($stage) => [
                'id' => $stage->id,
                'label' => $stage->name,
                'team_id' => $stage->team_id,
            ])
            ->values()
            ->toArray();
    }

    public function updatedStageId($value): void
    {
        // Limpar tarefas dependentes
        $this->dependentTaskId = null;
        $this->dependencyOptions = [];

        if (!$value) {
            return;
        }

        $stage = Stage::with('tasks')->find($value);

        if ($stage) {
            // Auto-preencher o time se não estiver definido
            if (!$this->teamId && $stage->team_id) {
                $this->teamId = $stage->team_id;
            }

            // Carregar tarefas do estágio para dependências
            $this->dependencyOptions = $stage->tasks
                ->sortBy('name')
                ->map(fn ($task) => [
                    'id' => $task->id,
                    'label' => $task->name,
                ])
                ->values()
                ->toArray();
        }
    }

    public function createTask(): void
    {
        $this->authorize('create', Task::class);

        $validated = $this->validate($this->rules(), [], [
            'stageId' => 'fase',
            'teamId' => 'time',
            'responsibleId' => 'responsável',
            'dependentTaskId' => 'tarefa dependente',
            'expectedStartAt' => 'início previsto',
            'expectedEndAt' => 'fim previsto',
            'estimatedHours' => 'horas estimadas',
        ]);

        $stage = Stage::with('project')->findOrFail($validated['stageId']);

        $lastOrder = optional($stage->tasks()->orderByDesc('order')->first())->order ?? 0;

        $task = Task::create([
            'tenant_id' => $stage->tenant_id ?? Auth::user()?->tenant_id,
            'stage_id' => $stage->id,
            'team_id' => $validated['teamId'] ?? $stage->team_id,
            'responsible_id' => $validated['responsibleId'] ?? Auth::id(),
            'dependent_task_id' => $validated['dependentTaskId'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'planned',
            'expected_start_at' => $validated['expectedStartAt'] ?: null,
            'expected_end_at' => $validated['expectedEndAt'] ?: null,
            'estimated_hours' => $validated['estimatedHours'] ?: null,
            'order' => $lastOrder + 1,
        ]);

        Flux::toast('Tarefa criada com sucesso', variant: 'success');

        $this->resetForm(preserveStage: true);
        $this->dependentTaskId = null;
        $this->updatedStageId($this->stageId);
        $this->dispatch('task-created', taskId: $task->id);
        $this->dispatch('close-form');
    }

    protected function rules(): array
    {
        return [
            'stageId' => ['required', 'exists:stages,id'],
            'teamId' => ['nullable', 'exists:teams,id'],
            'responsibleId' => ['nullable', 'exists:users,id'],
            'dependentTaskId' => ['nullable', 'exists:tasks,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'expectedStartAt' => ['nullable', 'date'],
            'expectedEndAt' => ['nullable', 'date', 'after_or_equal:expectedStartAt'],
            'estimatedHours' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function loadSelectOptions(): void
    {
        $user = Auth::user();
        $teams = $user?->teams()
            ->where('teams.is_active', true)
            ->orderBy('teams.name')
            ->get() ?? collect();

        $teamIds = $teams->pluck('id');

        $this->teamOptions = $teams
            ->map(fn ($team) => ['id' => $team->id, 'label' => $team->name])
            ->values()
            ->toArray();

        // Buscar TODOS os projetos
        // Se o usuário tem tenant_id, filtrar por ele; senão, buscar todos (superuser/admin)
        $projects = \App\Models\Project::query()
            ->when($user->tenant_id, fn ($query) => $query->where('tenant_id', $user->tenant_id))
            ->orderBy('name')
            ->get();

        $this->projectOptions = $projects
            ->map(fn ($project) => [
                'id' => $project->id,
                'label' => $project->name,
            ])
            ->values()
            ->toArray();

        // Estágios serão carregados quando selecionar um projeto
        $this->stageOptions = [];

        $responsibles = User::query()
            ->when($teamIds->isNotEmpty(), function ($query) use ($teamIds) {
                $query->whereHas('teams', function ($subQuery) use ($teamIds) {
                    $subQuery->whereIn('teams.id', $teamIds);
                });
            })
            ->orderBy('name')
            ->get();

        $this->responsibleOptions = $responsibles
            ->map(fn ($user) => ['id' => $user->id, 'label' => $user->name])
            ->values()
            ->toArray();
    }

    protected function resetForm(bool $preserveStage = false): void
    {
        $this->name = '';
        $this->description = null;
        $this->expectedStartAt = null;
        $this->expectedEndAt = null;
        $this->estimatedHours = null;
        $this->dependentTaskId = null;

        if (!$preserveStage) {
            $this->projectId = null;
            $this->stageId = null;
            $this->teamId = null;
            $this->stageOptions = [];
            $this->dependencyOptions = [];
        }
    }

    public function resetFields(): void
    {
        $this->resetForm();
        $this->projectId = null;
        $this->stageOptions = [];
        $this->dependencyOptions = [];
    }

    public function render()
    {
        return view('livewire.dashboard.create-task');
    }
}
