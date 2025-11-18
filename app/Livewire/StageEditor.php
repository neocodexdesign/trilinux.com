<?php

namespace App\Livewire;

use App\Models\Stage;
use App\Models\Task;
use Livewire\Component;

class StageEditor extends Component
{
    public ?Stage $stage = null;
    public $stageId = null;
    public $activeTab = 'details';
    public $showModal = false;

    // Stage fields
    public $name = '';
    public $description = '';
    public $status = 'planned';
    public $expected_start_at = '';
    public $expected_end_at = '';
    public $responsible_id = '';
    public $order = 0;

    // New Task
    public $newTaskName = '';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:planned,in_progress,paused,completed,cancelled',
        'expected_start_at' => 'nullable|date',
        'expected_end_at' => 'nullable|date|after_or_equal:expected_start_at',
        'responsible_id' => 'nullable|exists:users,id',
        'order' => 'required|integer|min:0',
    ];

    protected $listeners = ['openStageEditor' => 'openModal'];

    public function mount($stageId = null)
    {
        if ($stageId) {
            $this->stageId = $stageId;
            $this->loadStage();
        }
    }

    public function loadStage()
    {
        $this->stage = Stage::with(['project', 'responsible', 'tasks', 'notes', 'attachments'])
            ->findOrFail($this->stageId);

        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
        $this->status = $this->stage->status;
        $this->expected_start_at = $this->stage->expected_start_at?->format('Y-m-d');
        $this->expected_end_at = $this->stage->expected_end_at?->format('Y-m-d');
        $this->responsible_id = $this->stage->responsible_id;
        $this->order = $this->stage->order;
    }

    public function openModal($stageId = null)
    {
        $this->stageId = $stageId;

        if ($stageId) {
            $this->loadStage();
        } else {
            $this->reset(['name', 'description', 'status', 'expected_start_at', 'expected_end_at', 'responsible_id', 'order']);
            $this->stage = null;
        }

        $this->activeTab = 'details';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['stageId', 'stage', 'activeTab', 'newTaskName']);
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
            'responsible_id' => $this->responsible_id ?: null,
            'order' => $this->order,
        ];

        if ($this->stage) {
            $this->stage->update($data);
            $this->dispatch('stage-updated', stageId: $this->stage->id);
        }

        $this->closeModal();
    }

    public function deleteStage()
    {
        if ($this->stage) {
            $stageId = $this->stage->id;
            $this->stage->delete();
            $this->dispatch('stage-deleted', stageId: $stageId);
            $this->closeModal();
        }
    }

    public function createTask()
    {
        if (!$this->stage || !$this->newTaskName) {
            return;
        }

        $this->stage->tasks()->create([
            'tenant_id' => $this->stage->tenant_id,
            'name' => $this->newTaskName,
            'status' => 'planned',
            'order' => $this->stage->tasks()->max('order') + 1,
        ]);

        $this->newTaskName = '';
        $this->loadStage();
        $this->dispatch('task-created');
    }

    public function deleteTask($taskId)
    {
        if ($this->stage) {
            $task = $this->stage->tasks()->find($taskId);
            if ($task) {
                $task->delete();
                $this->loadStage();
                $this->dispatch('task-deleted');
            }
        }
    }

    public function render()
    {
        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.stage-editor', [
            'users' => $users,
        ]);
    }
}
