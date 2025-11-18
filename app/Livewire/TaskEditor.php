<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskEditor extends Component
{
    use WithFileUploads;

    public ?Task $task = null;
    public $taskId = null;
    public $activeTab = 'details';
    public $showModal = false;

    // Task fields
    public $name = '';
    public $description = '';
    public $status = 'planned';
    public $expected_start_at = '';
    public $expected_end_at = '';
    public $estimated_hours = '';
    public $responsible_id = '';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:planned,in_progress,paused,completed,cancelled',
        'expected_start_at' => 'nullable|date',
        'expected_end_at' => 'nullable|date|after_or_equal:expected_start_at',
        'estimated_hours' => 'nullable|numeric|min:0',
        'responsible_id' => 'nullable|exists:users,id',
    ];

    protected $listeners = ['openTaskEditor' => 'openModal'];

    public function mount($taskId = null)
    {
        if ($taskId) {
            $this->taskId = $taskId;
            $this->loadTask();
        }
    }

    public function loadTask()
    {
        $this->task = Task::with(['stage.project', 'responsible', 'notes', 'attachments'])
            ->findOrFail($this->taskId);

        $this->name = $this->task->name;
        $this->description = $this->task->description;
        $this->status = $this->task->status;
        $this->expected_start_at = $this->task->expected_start_at?->format('Y-m-d');
        $this->expected_end_at = $this->task->expected_end_at?->format('Y-m-d');
        $this->estimated_hours = $this->task->estimated_hours;
        $this->responsible_id = $this->task->responsible_id;
    }

    public function openModal($taskId = null)
    {
        $this->taskId = $taskId;

        if ($taskId) {
            $this->loadTask();
        } else {
            $this->reset(['name', 'description', 'status', 'expected_start_at', 'expected_end_at', 'estimated_hours', 'responsible_id']);
            $this->task = null;
        }

        $this->activeTab = 'details';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['taskId', 'task', 'activeTab']);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'expected_start_at' => $this->expected_start_at ?: null,
            'expected_end_at' => $this->expected_end_at ?: null,
            'estimated_hours' => $this->estimated_hours ?: null,
            'responsible_id' => $this->responsible_id ?: null,
        ];

        if ($this->task) {
            $this->task->update($data);
            $this->dispatch('task-updated', taskId: $this->task->id);
        }

        $this->closeModal();
    }

    public function deleteTask()
    {
        if ($this->task) {
            $taskId = $this->task->id;
            $this->task->delete();
            $this->dispatch('task-deleted', taskId: $taskId);
            $this->closeModal();
        }
    }

    public function render()
    {
        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.task-editor', [
            'users' => $users,
        ]);
    }
}
