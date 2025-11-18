<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Flux\Flux;
use Livewire\Component;

class TeamActiveTasks extends Component
{
    public function pauseTask($taskId)
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $taskId)
            ->where('status', 'in_progress')
            ->first();

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        try {
            $task->pauseForUser(auth()->id());
            Flux::toast('Tarefa pausada com sucesso', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Erro ao pausar tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function resumeTask($taskId)
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $taskId)
            ->where('status', 'paused')
            ->first();

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        try {
            $task->resumeForUser(auth()->id());
            Flux::toast('Tarefa retomada com sucesso', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Erro ao retomar tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function render()
    {
        $tasks = Task::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('status', ['in_progress', 'paused'])
            ->with(['stage.project', 'responsible', 'taskTimes'])
            ->orderBy('started_at', 'desc')
            ->get();

        // Group tasks by project and stage
        $groupedTasks = $tasks->groupBy(function ($task) {
            return $task->stage?->project?->id ?? 'no-project';
        })->map(function ($projectTasks) {
            return [
                'project' => $projectTasks->first()->stage?->project,
                'stages' => $projectTasks->groupBy(function ($task) {
                    return $task->stage?->id ?? 'no-stage';
                })->map(function ($stageTasks) {
                    return [
                        'stage' => $stageTasks->first()->stage,
                        'tasks' => $stageTasks,
                    ];
                })->values(),
            ];
        })->values();

        $totalTasks = $tasks->count();

        return view('livewire.tasks.team-active-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }
}
