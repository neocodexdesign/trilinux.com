<div class="relative flex flex-col overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/40 to-neutral-900 shadow-lg dark:border-amber-600/20">
    <div class="flex items-center justify-between border-b border-amber-500/20 bg-amber-950/30 px-4 py-3 backdrop-blur-sm">
        <div class="flex items-center gap-2">
            <svg class="size-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-amber-100">My Pending Tasks</h3>
        </div>
        <span class="rounded-full bg-amber-500/20 px-2.5 py-1 text-xs font-bold text-amber-300 ring-1 ring-amber-500/30">
            {{ $totalTasks }}
        </span>
    </div>

    <div class="flex-1 overflow-y-auto p-4">
        @forelse($groupedTasks as $projectGroup)
            <!-- Project Header -->
            <div class="mb-4 last:mb-0">
                <div class="mb-2 flex items-center gap-2 border-b border-amber-500/20 pb-2">
                    <svg class="size-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h4 class="text-sm font-semibold text-amber-200">
                        {{ $projectGroup['project']?->name ?? 'No Project' }}
                    </h4>
                </div>

                @foreach($projectGroup['stages'] as $stageGroup)
                    <!-- Stage Header -->
                    <div class="mb-3 ml-4">
                        <div class="mb-2 flex items-center gap-2">
                            <svg class="size-3.5 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h5 class="text-xs font-medium text-amber-300/80">
                                {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                            </h5>
                        </div>

                        <!-- Tasks in this Stage -->
                        <div class="ml-4 space-y-2">
                            @foreach($stageGroup['tasks'] as $task)
                                <div class="group relative overflow-hidden rounded-lg bg-amber-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-amber-950/30">
                                    <!-- Colored vertical bar -->
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-amber-400 to-amber-600"></div>

                                    <div class="p-3 pl-5 flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="mb-1">
                                                <h6 class="text-sm font-medium text-amber-50">
                                                    {{ $task->name }}
                                                </h6>
                                            </div>

                                            <div class="flex items-center gap-2 text-xs text-amber-300/70">
                                                @if($task->expected_start_at)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        Start {{ $task->expected_start_at->format('M d') }}
                                                    </span>
                                                @endif

                                                @if($task->estimated_hours)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $task->estimated_hours }}h
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <button
                                            wire:click.stop="startTask({{ $task->id }})"
                                            class="rounded-lg bg-amber-500 p-2 text-neutral-900 transition-all hover:bg-amber-400 active:scale-95"
                                            title="Iniciar tarefa">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="flex h-32 items-center justify-center text-center">
                <p class="text-sm text-amber-300/50">No pending tasks</p>
            </div>
        @endforelse
    </div>
</div>
