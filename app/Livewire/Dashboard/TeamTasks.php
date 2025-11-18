<?php

namespace App\Livewire\Dashboard;

use App\Models\Team;
use App\Models\Task;
use Flux\Flux;
use Livewire\Component;

class TeamTasks extends Component
{
    public $selectedTeamId = null;
    public $confirmingRevert = false;
    public $taskToRevert = null;

    protected $listeners = ['selectTeam'];

    public function mount()
    {
        // Auto-select first team the user belongs to
        $firstTeam = auth()->user()->teams()->first();
        if ($firstTeam) {
            $this->selectedTeamId = $firstTeam->id;
        }
    }

    public function selectTeam($teamId)
    {
        // Verify user belongs to this team before selecting
        $userBelongsToTeam = auth()->user()->teams()->where('teams.id', $teamId)->exists();

        if ($userBelongsToTeam) {
            $this->selectedTeamId = $teamId;
        } else {
            Flux::toast('You are not a member of this team', variant: 'danger');
        }
    }

    public function startTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            Flux::toast('Task not found', variant: 'danger');
            return;
        }

        if (!$task->canStart()) {
            Flux::toast('Cannot start this task', variant: 'danger');
            return;
        }

        // Check if user is in the team
        if ($task->team_id && !auth()->user()->isMemberOf($task->team)) {
            Flux::toast('You are not a member of this team', variant: 'danger');
            return;
        }

        try {
            $task->startForUser(auth()->id());
            Flux::toast('Task started successfully', variant: 'success');
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Flux::toast('Error starting task: ' . $e->getMessage(), variant: 'danger');
            \Log::error('TeamTasks startTask error', ['task_id' => $taskId, 'error' => $e->getMessage()]);
        }
    }

    public function confirmRevert($taskId)
    {
        $this->taskToRevert = $taskId;
        $this->confirmingRevert = true;
    }

    public function cancelRevert()
    {
        $this->confirmingRevert = false;
        $this->taskToRevert = null;
    }

    public function revertStart()
    {
        if (!$this->taskToRevert) {
            return;
        }

        $task = Task::find($this->taskToRevert);

        if (!$task || $task->status !== 'in_progress') {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            $this->cancelRevert();
            return;
        }

        try {
            $reverted = $task->revertStart(auth()->id());
            if ($reverted) {
                Flux::toast('Início da tarefa desfeito com sucesso', variant: 'success');
                $this->cancelRevert();
                $this->dispatch('$refresh');
            } else {
                Flux::toast('Não foi possível desfazer o início desta tarefa', variant: 'danger');
            }
        } catch (\Exception $e) {
            Flux::toast('Erro ao desfazer tarefa: ' . $e->getMessage(), variant: 'danger');
        }

        $this->cancelRevert();
    }

    public function pauseTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        try {
            $paused = $task->pauseForUser(auth()->id());

            if ($paused) {
                Flux::toast('Tarefa pausada com sucesso', variant: 'success');
                $this->dispatch('$refresh');
            } else {
                Flux::toast('Não foi possível pausar - nenhum tempo ativo encontrado', variant: 'danger');
            }
        } catch (\Exception $e) {
            Flux::toast('Erro ao pausar tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function resumeTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            Flux::toast('Task not found', variant: 'danger');
            return;
        }

        try {
            $task->resumeForUser(auth()->id());
            Flux::toast('Task resumed successfully', variant: 'success');
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Flux::toast('Error resuming task: ' . $e->getMessage(), variant: 'danger');
            \Log::error('TeamTasks resumeTask error', ['task_id' => $taskId, 'error' => $e->getMessage()]);
        }
    }

    public function completeTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            Flux::toast('Task not found', variant: 'danger');
            return;
        }

        try {
            $completed = $task->complete();
            if ($completed) {
                Flux::toast('Task completed successfully', variant: 'success');
                $this->dispatch('$refresh');
            } else {
                Flux::toast('Could not complete task', variant: 'danger');
            }
        } catch (\Exception $e) {
            Flux::toast('Error completing task: ' . $e->getMessage(), variant: 'danger');
            \Log::error('TeamTasks completeTask error', ['task_id' => $taskId, 'error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $selectedTeam = null;
        $groupedPlanned = [];
        $groupedOngoing = [];
        $groupedPaused = [];
        $groupedCompleted = [];

        if ($this->selectedTeamId) {
            $selectedTeam = Team::find($this->selectedTeamId);

            if ($selectedTeam) {
                // Get all tasks for the selected team
                $allTeamTasks = $this->getTasksForTeam($this->selectedTeamId);

                // Group by status
                $plannedTasks = $allTeamTasks->where('status', 'planned');
                $ongoingTasks = $allTeamTasks->where('status', 'in_progress');
                $pausedTasks = $allTeamTasks->where('status', 'paused');
                $completedTasks = $allTeamTasks->where('status', 'completed');

                // Group tasks hierarchically (Project > Stage > Tasks)
                $groupedPlanned = $this->groupTasksByProjectAndStage($plannedTasks);
                $groupedOngoing = $this->groupTasksByProjectAndStage($ongoingTasks);
                $groupedPaused = $this->groupTasksByProjectAndStage($pausedTasks);
                $groupedCompleted = $this->groupTasksByProjectAndStage($completedTasks);
            }
        }

        return view('livewire.dashboard.team-tasks', [
            'selectedTeam' => $selectedTeam,
            'groupedPlanned' => $groupedPlanned,
            'groupedOngoing' => $groupedOngoing,
            'groupedPaused' => $groupedPaused,
            'groupedCompleted' => $groupedCompleted,
            'totalPlanned' => count($groupedPlanned),
            'totalOngoing' => count($groupedOngoing),
            'totalPaused' => count($groupedPaused),
            'totalCompleted' => count($groupedCompleted),
        ]);
    }

    private function getTasksForTeam($teamId)
    {
        return Task::with(['stage.project', 'responsible', 'team'])
            ->whereIn('status', ['planned', 'in_progress', 'paused', 'completed'])
            ->whereNull('tasks.responsible_id')  // APENAS tarefas SEM responsável individual
            ->where(function ($query) use ($teamId) {
                // Tarefas atribuídas diretamente à equipe
                $query->where('tasks.team_id', $teamId)
                    // OU tarefas cujo Stage está atribuído à equipe
                    ->orWhereHas('stage', function ($stageQuery) use ($teamId) {
                        $stageQuery->where('stages.team_id', $teamId)
                            ->whereNull('stages.responsible_id');
                    })
                    // OU tarefas cujo Project está atribuído à equipe (através do Stage)
                    ->orWhereHas('stage.project', function ($projectQuery) use ($teamId) {
                        $projectQuery->where('projects.team_id', $teamId)
                            ->whereNull('projects.responsible_id');
                    });
            })
            ->orderBy('status')
            ->orderBy('expected_start_at')
            ->get();
    }

    private function groupTasksByProjectAndStage($tasks)
    {
        $grouped = [];

        foreach ($tasks as $task) {
            $projectId = $task->stage->project->id ?? 'no-project';
            $stageId = $task->stage->id ?? 'no-stage';

            if (!isset($grouped[$projectId])) {
                $grouped[$projectId] = [
                    'project' => $task->stage->project ?? null,
                    'stages' => []
                ];
            }

            if (!isset($grouped[$projectId]['stages'][$stageId])) {
                $grouped[$projectId]['stages'][$stageId] = [
                    'stage' => $task->stage ?? null,
                    'tasks' => []
                ];
            }

            $grouped[$projectId]['stages'][$stageId]['tasks'][] = $task;
        }

        return $grouped;
    }
}
