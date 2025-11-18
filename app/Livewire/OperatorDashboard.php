<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class OperatorDashboard extends Component
{
    public $selectedTask = null;
    public $showPreview = false;
    public $visibleCards = [];
    public $availableCards = ['pendentes', 'executando', 'pausadas', 'concluidas'];

    public $showTaskForm = false;
    public $newTask = [
        'project_id' => null,
        'stage_id' => null,
        'name' => '',
        'description' => '',
        'responsible_id' => null,
        'expected_start_at' => '',
        'expected_end_at' => '',
        'estimated_hours' => '',
    ];
    public $projects = [];
    public $stages = [];
    public $teamMembers = [];

    public function mount(): void
    {
        if (!in_array(Auth::user()->role, ['operator', 'manager', 'admin', 'superuser'])) {
            abort(403, 'Unauthorized');
        }

        $this->visibleCards = $this->availableCards;

        $this->loadProjects();
        $this->loadTeamMembers();
    }

    public function toggleCard(string $cardType): void
    {
        if (in_array($cardType, $this->visibleCards)) {
            $this->visibleCards = array_values(array_filter(
                $this->visibleCards,
                fn ($card) => $card !== $cardType
            ));
        } else {
            $this->visibleCards[] = $cardType;
        }
    }

    public function closeCard(string $cardType): void
    {
        $this->visibleCards = array_values(array_filter(
            $this->visibleCards,
            fn ($card) => $card !== $cardType
        ));
    }

    public function previewTask(int $taskId): void
    {
        $this->selectedTask = Task::with(['stage.project', 'responsible'])->find($taskId);
        $this->showPreview = (bool) $this->selectedTask;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->selectedTask = null;
    }

    public function executeTask(int $taskId): void
    {
        $task = Task::find($taskId);
        $user = Auth::user();

        if (!$task) {
            return;
        }

        if ($task->status === 'planned') {
            $task->startForUser($user->id);
        } elseif ($task->status === 'paused') {
            $task->resumeForUser($user->id);
        }

        $this->closePreview();
    }

    public function pauseTask(int $taskId): void
    {
        $task = Task::find($taskId);
        $user = Auth::user();

        if ($task) {
            $task->pauseForUser($user->id);
        }
    }

    public function resumeTask(int $taskId): void
    {
        $task = Task::find($taskId);
        $user = Auth::user();

        if ($task) {
            $task->resumeForUser($user->id);
        }
    }

    public function finishTask(int $taskId): void
    {
        \Log::info('Complete task called with ID: ' . $taskId);

        try {
            $task = Task::find($taskId);
            $user = Auth::user();

            if (!$task) {
                session()->flash('error', 'Tarefa não encontrada.');
                return;
            }

            $userTime = $task->getActiveOrPausedTimeForUser($user->id);

            if ($userTime) {
                $userTime->end();
                $task->refresh();

                if ($task->activeTimes()->count() === 0) {
                    $task->update([
                        'status' => 'completed',
                        'ended_at' => now(),
                    ]);
                }

                session()->flash('success', 'Tarefa concluída com sucesso!');
            } else {
                session()->flash('error', 'Nenhuma sessão ativa encontrada para esta tarefa.');
            }
        } catch (\Exception $e) {
            \Log::error('Error completing task: ' . $e->getMessage());
            session()->flash('error', 'Erro ao concluir tarefa: ' . $e->getMessage());
        }
    }

    public function openTaskForm(): void
    {
        $this->showTaskForm = true;
    }

    public function closeTaskForm(): void
    {
        $this->showTaskForm = false;
        $this->resetTaskForm();
    }

    public function updatedNewTaskProjectId($projectId): void
    {
        $this->newTask['stage_id'] = null;

        if (!$projectId) {
            $this->stages = [];
            return;
        }

        $this->stages = Stage::where('tenant_id', Auth::user()->tenant_id)
            ->where('project_id', $projectId)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function createTask(): void
    {
        $user = Auth::user();

        $this->validate([
            'newTask.project_id' => [
                'required',
                Rule::exists('projects', 'id')->where(fn ($query) => $query->where('tenant_id', $user->tenant_id)),
            ],
            'newTask.stage_id' => [
                'required',
                Rule::exists('stages', 'id')->where(function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);

                    if ($this->newTask['project_id']) {
                        $query->where('project_id', $this->newTask['project_id']);
                    }
                }),
            ],
            'newTask.name' => 'required|string|max:255',
            'newTask.description' => 'nullable|string',
            'newTask.responsible_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('tenant_id', $user->tenant_id)),
            ],
            'newTask.expected_start_at' => 'nullable|date',
            'newTask.expected_end_at' => 'nullable|date|after_or_equal:newTask.expected_start_at',
            'newTask.estimated_hours' => 'nullable|numeric|min:0|max:1000',
        ]);

        $stage = Stage::where('id', $this->newTask['stage_id'])
            ->where('tenant_id', $user->tenant_id)
            ->where('project_id', $this->newTask['project_id'])
            ->firstOrFail();

        Task::create([
            'tenant_id' => $user->tenant_id,
            'stage_id' => $stage->id,
            'name' => $this->newTask['name'],
            'description' => $this->newTask['description'] ?: null,
            'status' => 'planned',
            'expected_start_at' => $this->parseDate($this->newTask['expected_start_at']),
            'expected_end_at' => $this->parseDate($this->newTask['expected_end_at']),
            'responsible_id' => $this->newTask['responsible_id'] ?: null,
            'estimated_hours' => $this->newTask['estimated_hours'] ?: null,
        ]);

        session()->flash('success', 'Tarefa criada com sucesso!');

        $this->resetTaskForm();
        $this->showTaskForm = false;
    }

    protected function loadProjects(): void
    {
        $this->projects = Project::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function loadTeamMembers(): void
    {
        $this->teamMembers = User::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function resetTaskForm(): void
    {
        $this->newTask = [
            'project_id' => null,
            'stage_id' => null,
            'name' => '',
            'description' => '',
            'responsible_id' => null,
            'expected_start_at' => '',
            'expected_end_at' => '',
            'estimated_hours' => '',
        ];

        $this->stages = [];
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value);
    }

    public function render()
    {
        $user = Auth::user();

        $tasks = [
            'pendentes' => Task::where('tenant_id', $user->tenant_id)
                ->where('status', 'planned')
                ->where(function ($query) use ($user) {
                    $query->whereNull('responsible_id')
                        ->orWhere('responsible_id', $user->id);
                })
                ->with(['stage.project', 'responsible'])
                ->orderBy('expected_start_at')
                ->get(),

            'executando' => Task::where('tenant_id', $user->tenant_id)
                ->where('status', 'in_progress')
                ->whereHas('activeTimes', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->whereNull('ended_at')->whereNull('paused_at');
                })
                ->with([
                    'stage.project',
                    'responsible',
                    'activeTimes' => function ($query) use ($user) {
                        $query->where('user_id', $user->id)->whereNull('ended_at')->whereNull('paused_at');
                    },
                ])
                ->get(),

            'pausadas' => Task::where('tenant_id', $user->tenant_id)
                ->where(function ($query) {
                    $query->where('status', 'paused')
                        ->orWhere(function ($subquery) {
                            $subquery->where('status', 'in_progress')
                                ->whereHas('activeTimes', function ($timeQuery) {
                                    $timeQuery->whereNotNull('paused_at')->whereNull('ended_at');
                                });
                        });
                })
                ->whereHas('taskTimes', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['stage.project', 'responsible'])
                ->get(),

            'concluidas' => Task::where('tenant_id', $user->tenant_id)
                ->whereHas('taskTimes', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->whereNotNull('ended_at');
                })
                ->with([
                    'stage.project',
                    'responsible',
                    'taskTimes' => function ($query) use ($user) {
                        $query->where('user_id', $user->id)->whereNotNull('ended_at');
                    },
                ])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('livewire.operator-dashboard', compact('tasks'));
    }
}
