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
                <h1 class="text-2xl font-bold text-neutral-50">{{ $stage->name }}</h1>
                <p class="mt-1 text-sm text-neutral-400">
                    {{ $stage->project->name }}
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
                        @if($stage->notes->count() > 0)
                            <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $stage->notes->count() }}</span>
                        @endif
                    </button>
                    <button
                        @click="activeTab = 'attachments'"
                        class="border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                        :class="activeTab === 'attachments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-neutral-400 hover:text-neutral-200'">
                        Anexos
                        @if($stage->attachments->count() > 0)
                            <span class="ml-1 rounded-full bg-neutral-700 px-2 py-0.5 text-xs">{{ $stage->attachments->count() }}</span>
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
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium {{ $stage->status === 'completed' ? 'bg-green-500/20 text-green-300' : ($stage->status === 'in_progress' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300') }}">
                                    {{ match($stage->status) {
                                        'planned' => 'Planejado',
                                        'in_progress' => 'Em Progresso',
                                        'paused' => 'Pausado',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado',
                                        default => $stage->status
                                    } }}
                                </span>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Responsável</label>
                                @if($stage->responsible)
                                    <span class="inline-flex rounded-full bg-info/20 px-3 py-1 text-sm font-medium text-info">
                                        👤 {{ $stage->responsible->name }}
                                    </span>
                                @elseif($stage->team)
                                    <span class="inline-flex rounded-full bg-{{ $stage->team->color }}-500/20 px-3 py-1 text-sm font-medium text-{{ $stage->team->color }}-300">
                                        👥 {{ $stage->team->name }}
                                    </span>
                                @else
                                    <p class="text-neutral-400">Não atribuído</p>
                                @endif
                            </div>
                        </div>

                        @if($stage->description)
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Descrição</label>
                                <p class="text-neutral-100">{{ $stage->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Início Esperado</label>
                                <p class="text-neutral-100">{{ $stage->expected_start_at?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Fim Esperado</label>
                                <p class="text-neutral-100">{{ $stage->expected_end_at?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Ordem</label>
                                <p class="text-neutral-100">#{{ $stage->order }}</p>
                            </div>
                        </div>

                        @if($stage->dependentStage)
                            <div>
                                <label class="mb-2 block text-sm font-medium text-neutral-300">Depende da Etapa</label>
                                <p class="text-neutral-100">{{ $stage->dependentStage->name }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Notes Tab -->
                    <div x-show="activeTab === 'notes'">
                        <livewire:stage-editor :stage="$stage" :key="'manage-notes-'.$stage->id" />
                    </div>

                    <!-- Attachments Tab -->
                    <div x-show="activeTab === 'attachments'">
                        <livewire:attachment-manager :attachable="$stage" :key="'manage-attachments-'.$stage->id" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
