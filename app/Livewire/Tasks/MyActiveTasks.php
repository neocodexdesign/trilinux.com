<?php

namespace App\Livewire\Tasks;

use Flux\Flux;
use Livewire\Component;

class MyActiveTasks extends Component
{
    public $confirmingRevert = false;
    public $taskToRevert = null;

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

        $task = auth()->user()
            ->assignedTasks()
            ->where('id', $this->taskToRevert)
            ->where('status', 'in_progress')
            ->first();

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            $this->cancelRevert();
            return;
        }

        try {
            $reverted = $task->revertStart(auth()->id());
            if ($reverted) {
                Flux::toast('Início da tarefa desfeito com sucesso', variant: 'success');
                $this->cancelRevert();
                return $this->redirect(route('tasks.my.active'), navigate: true);
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
        $task = auth()->user()
            ->assignedTasks()
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

    public function completeTask($taskId)
    {
        $task = auth()->user()
            ->assignedTasks()
            ->where('id', $taskId)
            ->whereIn('status', ['in_progress', 'paused'])
            ->first();

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        try {
            $completed = $task->complete();
            if ($completed) {
                Flux::toast('Tarefa concluída com sucesso', variant: 'success');
                return $this->redirect(route('tasks.my.active'), navigate: true);
            }

            Flux::toast('Não foi possível concluir a tarefa', variant: 'danger');
        } catch (\Exception $e) {
            Flux::toast('Erro ao concluir tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

    public function render()
    {
        $tasks = auth()->user()
            ->assignedTasks()
            ->whereIn('status', ['in_progress', 'paused'])
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

        return view('livewire.tasks.my-active-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }
}
