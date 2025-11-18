<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4">
            <div class="relative w-full max-w-2xl rounded-xl bg-neutral-800 shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Editar Tarefa</h2>
                    <button
                        wire:click="closeModal"
                        class="rounded-lg p-2 text-neutral-400 transition hover:bg-neutral-700 hover:text-white"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="updateTask" class="max-h-[70vh] overflow-y-auto p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Projeto -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Projeto</label>
                            <select wire:model.live="projectId" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Selecione o projeto</option>
                                @foreach($projectOptions as $project)
                                    <option value="{{ $project['id'] }}">{{ $project['label'] }}</option>
                                @endforeach
                            </select>
                            @error('projectId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Estágio -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Estágio</label>
                            <select
                                wire:model.live="stageId"
                                @disabled(!$projectId)
                                class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50"
                            >
                                <option value="">{{ $projectId ? 'Selecione o estágio' : 'Primeiro selecione um projeto' }}</option>
                                @foreach($stageOptions as $stage)
                                    <option value="{{ $stage['id'] }}">{{ $stage['label'] }}</option>
                                @endforeach
                            </select>
                            @error('stageId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Time</label>
                            <select wire:model="teamId" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Selecionar time</option>
                                @foreach($teamOptions as $team)
                                    <option value="{{ $team['id'] }}">{{ $team['label'] }}</option>
                                @endforeach
                            </select>
                            @error('teamId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Responsável -->
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Responsável</label>
                            <select wire:model="responsibleId" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Selecionar responsável</option>
                                @foreach($responsibleOptions as $responsible)
                                    <option value="{{ $responsible['id'] }}">{{ $responsible['label'] }}</option>
                                @endforeach
                            </select>
                            @error('responsibleId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nome -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Nome da Tarefa</label>
                            <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Descrição</label>
                            <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Datas e Horas -->
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Início Previsto</label>
                            <input type="date" wire:model="expectedStartAt" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            @error('expectedStartAt') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Fim Previsto</label>
                            <input type="date" wire:model="expectedEndAt" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            @error('expectedEndAt') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Horas Estimadas</label>
                            <input type="number" step="0.5" min="0" wire:model="estimatedHours" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            @error('estimatedHours') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Dependência -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Depende da tarefa (opcional)</label>
                            <select
                                wire:model="dependentTaskId"
                                @disabled(!$stageId)
                                class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50"
                            >
                                <option value="">{{ $stageId ? 'Nenhuma dependência' : 'Primeiro selecione um estágio' }}</option>
                                @foreach($dependencyOptions as $taskOption)
                                    <option value="{{ $taskOption['id'] }}">{{ $taskOption['label'] }}</option>
                                @endforeach
                            </select>
                            @error('dependentTaskId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-neutral-700 pt-4">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-neutral-600 bg-neutral-900 px-4 py-2 text-sm font-medium text-neutral-300 transition hover:border-neutral-500 hover:bg-neutral-800 hover:text-white">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-neutral-800">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
