<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PendingTasks extends Component
{
    use WithPagination;

    public int $perPage = 5;

    public function editTask($taskId)
    {
        $this->dispatch('edit-task', taskId: $taskId);
    }

    #[On('task-updated')]
    #[On('task-created')]
    public function refreshTasks()
    {
        // Força a re-renderização do componente
    }

    public function deleteTask($taskId)
    {
        $task = auth()->user()
            ->assignedTasks()
            ->where('id', $taskId)
            ->first();

        if (!$task) {
            Flux::toast('Tarefa não encontrada', variant: 'danger');
            return;
        }

        try {
            $task->delete();
            Flux::toast('Tarefa excluída com sucesso', variant: 'success');
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            Flux::toast('Erro ao excluir tarefa: ' . $e->getMessage(), variant: 'danger');
        }
    }

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
            return $this->redirect(route('dashboard'), navigate: true);
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

        return view('livewire.dashboard.pending-tasks', [
            'groupedTasks' => $groupedTasks,
            'totalTasks' => $totalTasks,
        ]);
    }

    public function goToPage($page)
    {
        $this->setPage($page);
    }
}
