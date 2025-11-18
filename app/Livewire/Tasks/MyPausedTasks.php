<?php

namespace App\Livewire\Tasks;

use Flux\Flux;
use Livewire\Component;

class MyPausedTasks extends Component
{
    public function resumeTask($taskId)
    {
        $task = auth()->user()
            ->assignedTasks()
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
        $tasks = auth()->user()
            ->assignedTasks()
            ->where('status', 'paused')
            ->with(['stage.project', 'taskTimes'])
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

        return view('livewire.tasks.my-paused-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }
}
