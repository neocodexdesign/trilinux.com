<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Review;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskManager extends Component
{
    use AuthorizesRequests;

    public Task $task;
    public bool $showReviewModal = false;
    public string $reviewAction = '';
    public string $reviewNotes = '';

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function startTask()
    {
        $this->authorize('start', $this->task);

        if ($this->task->canStart()) {
            $this->task->start();
            $this->task->refresh();
            
            session()->flash('success', 'Task started successfully!');
            $this->dispatch('task-updated', taskId: $this->task->id);
        } else {
            session()->flash('error', 'Cannot start this task. Check dependencies.');
        }
    }

    public function pauseTask()
    {
        $this->authorize('pause', $this->task);

        if ($this->task->pause()) {
            $this->task->refresh();
            
            session()->flash('success', 'Task paused successfully!');
            $this->dispatch('task-updated', taskId: $this->task->id);
        } else {
            session()->flash('error', 'Cannot pause this task.');
        }
    }

    public function resumeTask()
    {
        $this->authorize('resume', $this->task);

        if ($this->task->resume()) {
            $this->task->refresh();
            
            session()->flash('success', 'Task resumed successfully!');
            $this->dispatch('task-updated', taskId: $this->task->id);
        } else {
            session()->flash('error', 'Cannot resume this task.');
        }
    }

    public function completeTask()
    {
        $this->authorize('complete', $this->task);

        if ($this->task->complete()) {
            $this->task->refresh();
            
            session()->flash('success', 'Task completed successfully!');
            $this->dispatch('task-updated', taskId: $this->task->id);
        } else {
            session()->flash('error', 'Cannot complete this task.');
        }
    }

    public function openReviewModal(string $action)
    {
        $this->authorize('review', $this->task);
        
        $this->reviewAction = $action;
        $this->reviewNotes = '';
        $this->showReviewModal = true;
    }

    public function submitReview()
    {
        $this->validate([
            'reviewAction' => 'required|in:approved,paused,restarted,rejected',
            'reviewNotes' => 'nullable|string|max:1000',
        ]);

        Review::createReview(
            $this->task,
            auth()->user(),
            $this->reviewAction,
            $this->reviewNotes
        );

        // Apply the review action
        switch ($this->reviewAction) {
            case 'approved':
                // Mark as approved, could trigger next stage
                break;
            case 'paused':
                $this->task->pause();
                break;
            case 'restarted':
                $this->task->update(['status' => 'planned']);
                break;
            case 'rejected':
                $this->task->update(['status' => 'cancelled']);
                break;
        }

        $this->task->refresh();
        $this->showReviewModal = false;
        
        session()->flash('success', 'Review submitted successfully!');
        $this->dispatch('task-updated', taskId: $this->task->id);
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->reviewAction = '';
        $this->reviewNotes = '';
    }

    #[On('task-updated')]
    public function refreshTask($taskId)
    {
        if ($this->task->id == $taskId) {
            $this->task->refresh();
        }
    }

    public function getStatusColorProperty()
    {
        return match($this->task->status) {
            'planned' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'paused' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        return view('livewire.task-manager');
    }
}