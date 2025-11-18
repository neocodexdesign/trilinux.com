<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-neutral-400 hover:text-neutral-200">
                <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
        </div>

        <div class="rounded-xl border border-neutral-700 bg-neutral-900 shadow-2xl">
            <!-- Header -->
            <div class="border-b border-neutral-700 px-6 py-4">
                <h1 class="text-2xl font-bold text-neutral-50">{{ $task->name }}</h1>
                <p class="mt-1 text-sm text-neutral-400">
                    {{ $task->stage->project->name }} › {{ $task->stage->name }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-neutral-700" x-data="{ activeTab: 'details' }">
                <nav class="flex space-x-6 px-6">
                    <button
                        @click="activeTab = 'details'"
                        class="border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                        :class="activeTab === 'details' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200'">
                        Detalhes
                    </button>
                    <button
                        @click="activeTab = 'notes'"
                        class="border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                        :class="activeTab === 'notes' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200'">
                        Notas
                        @if($task->notes->count() > 0)
                            <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $task->notes->count() }}</span>
                        @endif
                    </button>
                    <button
                        @click="activeTab = 'attachments'"
                        class="border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                        :class="activeTab === 'attachments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200'">
                        Anexos
                        @if($task->attachments->count() > 0)
                            <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $task->attachments->count() }}</span>
                        @endif
                    </button>
                </nav>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Details Tab -->
                    <div x-show="activeTab === 'details'" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Status</label>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium {{ $task->status === 'completed' ? 'bg-green-500/20 text-green-300' : ($task->status === 'in_progress' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Responsável</label>
                                <p class="text-neutral-100">{{ $task->responsible->name ?? 'Não atribuído' }}</p>
                            </div>
                        </div>

                        @if($task->description)
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Descrição</label>
                                <p class="text-neutral-100">{{ $task->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Início Esperado</label>
                                <p class="text-neutral-100">{{ $task->expected_start_at?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Fim Esperado</label>
                                <p class="text-neutral-100">{{ $task->expected_end_at?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Horas Estimadas</label>
                                <p class="text-neutral-100">{{ $task->estimated_hours ?? '-' }}h</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Tab -->
                    <div x-show="activeTab === 'notes'">
                        <livewire:task-notes :task="$task" :key="'manage-notes-'.$task->id" />
                    </div>

                    <!-- Attachments Tab -->
                    <div x-show="activeTab === 'attachments'">
                        <livewire:attachment-manager :attachable="$task" :key="'manage-attachments-'.$task->id" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
