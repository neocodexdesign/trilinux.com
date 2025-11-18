<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TaskCounter extends Component
{
    public int $count = 0;

    public function __construct(
        public string $type = 'my',
        public string $status = 'pending'
    ) {
        $this->count = $this->getTaskCount();
    }

    protected function getTaskCount(): int
    {
        $query = $this->type === 'my'
            ? auth()->user()->assignedTasks()
            : \App\Models\Task::where('tenant_id', auth()->user()->tenant_id);

        // Filter by status
        match ($this->status) {
            'pending' => $query->where('status', 'planned'),
            'active' => $query->whereIn('status', ['in_progress', 'paused']),
            'ongoing' => $query->whereIn('status', ['in_progress']),
            'paused' => $query->where('status', 'paused'),
            'completed' => $query->where('status', 'completed'),
            default => $query,
        };

        return $query->count();
    }

    public function render(): View|Closure|string
    {
        return view('components.task-counter');
    }
}
