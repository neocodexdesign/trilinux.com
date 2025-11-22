<div x-data="{
    cardExpanded: {{ $totalTasks > 0 ? 'true' : 'false' }},
    showDeleteModal: false,
    taskToDelete: null,
    taskName: '',
    expandedProjects: {},
    expandedStages: {},
    expandedTasks: {},
    toggleCard() {
        this.cardExpanded = !this.cardExpanded;
    },
    toggleProject(projectId) {
        this.expandedProjects[projectId] = !this.expandedProjects[projectId];
    },
    toggleStage(stageId) {
        this.expandedStages[stageId] = !this.expandedStages[stageId];
    },
    toggleTask(taskId) {
        this.expandedTasks[taskId] = !this.expandedTasks[taskId];
    }
}" class="relative flex flex-col overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/40 to-neutral-900 shadow-lg dark:border-amber-600/20">
    <div class="flex items-center justify-between border-b border-amber-500/20 bg-amber-950/30 px-4 py-3 backdrop-blur-sm cursor-pointer hover:bg-amber-950/40 transition-colors"
         @click="toggleCard()">
        <div class="flex items-center gap-2">
            <!-- Ícone de expand/collapse do card -->
            <svg x-show="cardExpanded" class="size-4 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg x-show="!cardExpanded" class="size-4 text-amber-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>

            <svg class="size-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-amber-100">My Pending Tasks</h3>
        </div>
        <span class="rounded-full bg-amber-500/20 px-2.5 py-1 text-xs font-bold text-amber-300 ring-1 ring-amber-500/30">
            {{ $totalTasks }}
        </span>
    </div>

    <div x-show="cardExpanded"
         x-collapse
         class="flex-1 p-4">
        @forelse($groupedTasks as $index => $projectGroup)
            @php
                $projectId = $projectGroup['project']?->id ?? 'no-project-' . $index;
            @endphp
            <!-- Project Header -->
            <div class="mb-4 last:mb-0"
                 x-data="{ projectExpanded: true }"
                 x-init="expandedProjects['{{ $projectId }}'] = true">
                <div class="mb-2 flex items-center gap-2 border-b border-amber-500/20 pb-2 cursor-pointer hover:bg-amber-950/10 transition-colors rounded px-2 py-1"
                     @click="expandedProjects['{{ $projectId }}'] = !expandedProjects['{{ $projectId }}']">
                    <!-- Ícone de expand/collapse do projeto -->
                    <svg x-show="expandedProjects['{{ $projectId }}']" class="size-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="!expandedProjects['{{ $projectId }}']" class="size-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>

                    <svg class="size-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h4 class="text-sm font-semibold text-amber-200">
                        {{ $projectGroup['project']?->name ?? 'No Project' }}
                    </h4>
                    <span class="ml-auto text-xs text-amber-400/60">({{ count($projectGroup['stages']) }} stages)</span>
                </div>

                <div x-show="expandedProjects['{{ $projectId }}']" x-collapse>
                @foreach($projectGroup['stages'] as $stageIndex => $stageGroup)
                    @php
                        $stageId = $stageGroup['stage']?->id ?? 'no-stage-' . $projectId . '-' . $stageIndex;
                    @endphp
                    <!-- Stage Header -->
                    <div class="mb-3 ml-4" x-init="expandedStages['{{ $stageId }}'] = false">
                        <div class="mb-2 flex items-center gap-2 cursor-pointer hover:bg-amber-950/10 transition-colors rounded px-2 py-1"
                             @click="expandedStages['{{ $stageId }}'] = !expandedStages['{{ $stageId }}']">
                            <!-- Ícone de expand/collapse do estágio -->
                            <svg x-show="expandedStages['{{ $stageId }}']" class="size-3 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <svg x-show="!expandedStages['{{ $stageId }}']" class="size-3 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>

                            <svg class="size-3.5 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h5 class="text-xs font-medium text-amber-300/80">
                                {{ $stageGroup['stage']?->name ?? 'No Stage' }}
                            </h5>
                            <span class="ml-auto text-xs text-amber-400/50">({{ count($stageGroup['tasks']) }} tasks)</span>
                        </div>

                        <!-- Tasks in this Stage -->
                        <div x-show="expandedStages['{{ $stageId }}']" x-collapse class="ml-4 space-y-2">
                            @foreach($stageGroup['tasks'] as $task)
                                <div class="group relative overflow-hidden rounded-lg bg-amber-950/20 shadow-sm backdrop-blur-sm transition-all hover:bg-amber-950/30"
                                     x-data="{ taskExpanded: false }"
                                     x-init="expandedTasks[{{ $task->id }}] = false">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-amber-400 to-amber-600"></div>
                                    <!-- Task Header (sempre visível) -->
                                    <div class="p-3 pl-5 cursor-pointer"
                                         @click="expandedTasks[{{ $task->id }}] = !expandedTasks[{{ $task->id }}]">
                                        <div class="flex items-center gap-2">
                                            <!-- Ícone de expand/collapse da tarefa -->
                                            <svg x-show="expandedTasks[{{ $task->id }}]" class="size-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <svg x-show="!expandedTasks[{{ $task->id }}]" class="size-3 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>

                                            <h6 class="text-sm font-medium text-amber-50 flex-1">
                                                {{ $task->name }}
                                            </h6>
                                        </div>

                                        <!-- Media Icons (below task name) -->
                                        @php
                                            $media = $task->getMediaSummary();
                                        @endphp
                                        @if($media['total'] > 0)
                                            <div class="flex items-center gap-1.5 mt-2 ml-5" @click.stop>
                                                <x-task-media-icons :task="$task" />
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Task Details (minimizável) -->
                                    <div x-show="expandedTasks[{{ $task->id }}]" x-collapse class="px-3 pl-5 pb-3">
                                        <div class="mb-3">
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

                                        <!-- Botões de Ação (só aparecem quando expandido) -->
                                        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-amber-500/20">
                                            @php
                                                $notesCount = $task->notes()->count();
                                                $attachmentsCount = $task->attachments()->count();
                                            @endphp

                                            <!-- Botão Notas -->
                                            <button
                                                wire:click.stop="$dispatch('open-task-notes', { taskId: {{ $task->id }} })"
                                                @click.stop
                                                class="relative rounded bg-amber-600/40 p-2 text-white transition-all hover:bg-amber-600/60 active:scale-95"
                                                title="Notas">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                @if($notesCount > 0)
                                                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">
                                                        {{ $notesCount }}
                                                    </span>
                                                @endif
                                            </button>

                                            <!-- Botão Anexos -->
                                            <button
                                                wire:click.stop="$dispatch('open-task-attachments', { taskId: {{ $task->id }} })"
                                                @click.stop
                                                class="relative rounded bg-purple-500/30 p-2 text-white transition-all hover:bg-purple-500/50 active:scale-95"
                                                title="Anexos">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                @if($attachmentsCount > 0)
                                                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-purple-500 text-[10px] font-bold text-white">
                                                        {{ $attachmentsCount }}
                                                    </span>
                                                @endif
                                            </button>

                                            <div class="w-px h-6 bg-amber-500/30 self-center"></div>

                                            <!-- Botão Editar -->
                                            <button
                                                wire:click.stop="editTask({{ $task->id }})"
                                                @click.stop
                                                class="rounded-lg bg-blue-600 p-2 text-white transition-all hover:bg-blue-500 active:scale-95"
                                                title="Editar tarefa">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            <!-- Botão Excluir -->
                                            <button
                                                @click.stop="showDeleteModal = true; taskToDelete = {{ $task->id }}; taskName = '{{ addslashes($task->name) }}'"
                                                class="rounded-lg bg-red-600 p-2 text-white transition-all hover:bg-red-500 active:scale-95"
                                                title="Excluir tarefa">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>

                                            <!-- Botão Iniciar -->
                                            <button
                                                wire:click.stop="startTask({{ $task->id }})"
                                                @click.stop
                                                class="rounded-lg bg-amber-500 p-2 text-neutral-900 transition-all hover:bg-amber-400 active:scale-95"
                                                title="Iniciar tarefa">
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
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
            <div class="flex h-32 items-center justify-center text-center">
                <p class="text-sm text-amber-300/50">No pending tasks</p>
            </div>
        @endforelse
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="showDeleteModal = false"
    >
        <div
            class="relative w-full max-w-md rounded-xl bg-neutral-800 shadow-2xl"
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Header -->
            <div class="flex items-center gap-3 border-b border-neutral-700 px-6 py-4">
                <div class="flex size-10 items-center justify-center rounded-full bg-red-500/10">
                    <svg class="size-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white">Confirmar Exclusão</h2>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <p class="text-sm text-neutral-300">
                    Tem certeza que deseja excluir a tarefa
                    <span class="font-semibold text-white" x-text="taskName"></span>?
                </p>
                <p class="mt-2 text-sm text-neutral-400">
                    Esta ação não pode ser desfeita.
                </p>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-neutral-700 px-6 py-4">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="rounded-lg border border-neutral-600 bg-neutral-900 px-4 py-2 text-sm font-medium text-neutral-300 transition hover:border-neutral-500 hover:bg-neutral-800 hover:text-white"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="$wire.deleteTask(taskToDelete); showDeleteModal = false"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-neutral-800"
                >
                    Excluir Tarefa
                </button>
            </div>
        </div>
    </div>
</div>
