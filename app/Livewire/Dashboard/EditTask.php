<?php

namespace App\Livewire\Dashboard;

use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EditTask extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;
    public ?int $taskId = null;
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

    #[On('edit-task')]
    public function openModal($taskId)
    {
        $this->taskId = $taskId;
        $task = Task::with(['stage.project'])->find($taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        // Carregar dados da tarefa
        $this->name = $task->name;
        $this->description = $task->description;
        $this->stageId = $task->stage_id;
        $this->projectId = $task->stage?->project_id;
        $this->teamId = $task->team_id;
        $this->responsibleId = $task->responsible_id;
        $this->dependentTaskId = $task->dependent_task_id;
        $this->expectedStartAt = $task->expected_start_at?->format('Y-m-d');
        $this->expectedEndAt = $task->expected_end_at?->format('Y-m-d');
        $this->estimatedHours = $task->estimated_hours;

        $this->loadSelectOptions();
        $this->showModal = true;
    }

    public function updatedProjectId($value): void
    {
        $this->stageId = null;
        $this->stageOptions = [];
        $this->dependentTaskId = null;
        $this->dependencyOptions = [];

        if (!$value) {
            return;
        }

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
        $this->dependentTaskId = null;
        $this->dependencyOptions = [];

        if (!$value) {
            return;
        }

        $stage = Stage::with('tasks')->find($value);

        if ($stage) {
            if (!$this->teamId && $stage->team_id) {
                $this->teamId = $stage->team_id;
            }

            $this->dependencyOptions = $stage->tasks
                ->where('id', '!=', $this->taskId) // Excluir a própria tarefa
                ->sortBy('name')
                ->map(fn ($task) => [
                    'id' => $task->id,
                    'label' => $task->name,
                ])
                ->values()
                ->toArray();
        }
    }

    public function updateTask(): void
    {
        $task = Task::find($this->taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        $validated = $this->validate($this->rules(), [], [
            'stageId' => 'fase',
            'teamId' => 'time',
            'responsibleId' => 'responsável',
            'dependentTaskId' => 'tarefa dependente',
            'expectedStartAt' => 'início previsto',
            'expectedEndAt' => 'fim previsto',
            'estimatedHours' => 'horas estimadas',
        ]);

        $task->update([
            'stage_id' => $validated['stageId'],
            'team_id' => $validated['teamId'] ?? null,
            'responsible_id' => $validated['responsibleId'] ?? null,
            'dependent_task_id' => $validated['dependentTaskId'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_start_at' => $validated['expectedStartAt'] ?: null,
            'expected_end_at' => $validated['expectedEndAt'] ?: null,
            'estimated_hours' => $validated['estimatedHours'] ?: null,
        ]);

        Flux::toast('Tarefa atualizada com sucesso', variant: 'success');

        $this->closeModal();
        $this->dispatch('task-updated', taskId: $task->id);
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

        // Carregar estágios do projeto selecionado
        if ($this->projectId) {
            $this->updatedProjectId($this->projectId);
        }

        // Carregar dependências do estágio selecionado
        if ($this->stageId) {
            $this->updatedStageId($this->stageId);
        }

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

    public function closeModal()
    {
        $this->showModal = false;
        $this->taskId = null;
        $this->projectId = null;
        $this->stageId = null;
        $this->teamId = null;
        $this->responsibleId = null;
        $this->dependentTaskId = null;
        $this->name = '';
        $this->description = null;
        $this->expectedStartAt = null;
        $this->expectedEndAt = null;
        $this->estimatedHours = null;
        $this->stageOptions = [];
        $this->dependencyOptions = [];
    }

    public function render()
    {
        return view('livewire.dashboard.edit-task');
    }
}
