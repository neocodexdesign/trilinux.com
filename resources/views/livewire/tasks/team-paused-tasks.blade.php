<div class="relative flex h-full flex-col overflow-hidden rounded-xl border border-yellow-500/30 bg-gradient-to-br from-yellow-950/40 to-neutral-900 shadow-lg dark:border-yellow-600/20">
    <div class="flex items-center justify-between border-b border-yellow-500/20 bg-yellow-950/30 px-6 py-4 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <svg class="size-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-yellow-100">Paused Tasks</h3>
        </div>
        <span class="rounded-full bg-yellow-500/20 px-3 py-1.5 text-sm font-bold text-yellow-300 ring-1 ring-yellow-500/30">
            {{ $totalTasks }} {{ Str::plural('task', $totalTasks) }}
        </span>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        @forelse($groupedTasks as $projectIndex => $projectGroup)
            <div class="mb-6 last:mb-0" x-data="{ projectOpen: true }">
                <button @click="projectOpen = !projectOpen" class="mb-3 flex w-full items-center gap-3 border-b border-yellow-500/20 pb-3 text-left transition-colors hover:border-yellow-500/30">
                    <svg x-show="projectOpen" class="size-5 shrink-0 text-yellow-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="!projectOpen" class="size-5 shrink-0 text-yellow-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <svg class="size-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h4 class="text-base font-semibold text-yellow-200">
                        {{ $projectGroup['project']?->name ?? 'No Project' }}
                    </h4>
                </button>

                <div x-show="projectOpen" x-collapse>
                @foreach($projectGroup['stages'] as $stageGroup)
                    <div class="mb-4 ml-6" x-data="{ stageOpen: true }">
                        <button @click="stageOpen = !stageOpen" class="mb-3 flex w-full items-center gap-2 text-left transition-colors hover:text-yellow-200">
                            <svg x-show="stageOpen" class="size-4 shrink-0 text-yellow-400/70 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!stageOpen" class="size-4 shrink-0 text-yellow-400/70 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <svg class="size-4 text-yellow-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h5 class="text-sm font-medium text-yellow-300/80">
                                {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                            </h5>
                        </button>

                        <div x-show="stageOpen" x-collapse class="ml-6 space-y-3">
                            @foreach($stageGroup['tasks'] as $task)
                                <div class="group relative overflow-hidden rounded-lg bg-yellow-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-yellow-950/30">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-yellow-400 to-yellow-600"></div>

                                    <div class="p-4 pl-6">
                                        <div class="mb-2 flex items-start justify-between gap-3">
                                            <h6 class="text-base font-medium text-yellow-50">
                                                {{ $task->name }}
                                            </h6>
                                            <div class="flex items-center gap-2">
                                                <livewire:task-notes :task="$task" :key="'task-notes-'.$task->id" />
                                                <span class="rounded px-2 py-0.5 text-xs font-medium bg-yellow-500/20 text-yellow-300 ring-1 ring-yellow-500/30 whitespace-nowrap">
                                                    Paused
                                                </span>
                                            </div>
                                        </div>

                                        @if($task->description)
                                            <p class="mb-3 text-sm text-yellow-200/60">
                                                {{ Str::limit($task->description, 150) }}
                                            </p>
                                        @endif

                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 text-xs text-yellow-300/70">
                                            @if($task->started_at)
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Started {{ $task->started_at->diffForHumans() }}
                                                </span>
                                            @endif

                                            @if($task->estimated_hours)
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    {{ $task->estimated_hours }}h est.
                                                </span>
                                            @endif

                                            <span class="flex items-center gap-1.5 font-semibold text-yellow-200">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Work: {{ $task->getFormattedWorkTime() }}
                                            </span>

                                            <span class="flex items-center gap-1.5 font-semibold text-yellow-200">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Pause: {{ $task->getFormattedPauseTime() }}
                                            </span>

                                            <span class="flex items-center gap-1.5 font-semibold text-yellow-200">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Total: {{ $task->getFormattedElapsedTime() }}
                                            </span>
                                            </div>

                                            <button wire:click="resumeTask({{ $task->id }})" class="rounded bg-green-500/30 px-2.5 py-1 text-xs font-medium text-green-100 transition-all hover:bg-green-500/50 active:scale-95 whitespace-nowrap">
                                                Resume
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @empty
            <div class="flex h-full items-center justify-center text-center">
                <div class="text-center">
                    <svg class="mx-auto mb-4 size-16 text-yellow-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-base text-yellow-300/50">No paused tasks</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
