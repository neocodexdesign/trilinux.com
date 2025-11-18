<?php

namespace App\Livewire\Tasks;

use Flux\Flux;
use Livewire\Component;

class MyPendingTasks extends Component
{
    public function startTask($taskId)
    {
        $task = auth()->user()
            ->assignedTasks()
            ->where('id', $taskId)
            ->first();

        if (!$task || !$task->canStart()) {
            Flux::toast('Não foi possível iniciar esta tarefa', variant: 'danger');
            return;
        }

        try {
            $task->startForUser(auth()->id());
            Flux::toast('Tarefa iniciada com sucesso', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Erro ao iniciar tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function render()
    {
        $tasks = auth()->user()
            ->assignedTasks()
            ->where('status', 'planned')
            ->with(['stage.project'])
            ->orderBy('expected_start_at')
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

        return view('livewire.tasks.my-pending-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }
}
