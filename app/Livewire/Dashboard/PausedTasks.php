<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class PausedTasks extends Component
{
    use WithPagination;

    public int $perPage = 5;

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
            return $this->redirect(route('dashboard'), navigate: true);
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

        return view('livewire.dashboard.paused-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }

    public function goToPage($page)
    {
        $this->setPage($page);
    }
}
