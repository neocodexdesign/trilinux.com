<div x-data="{ open: false }" @close-form.window="open = false" class="relative">
    <!-- Botão discreto -->
    <button
        @click="open = !open"
        type="button"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-neutral-900"
    >
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span x-text="open ? 'Fechar' : 'Nova Tarefa'">Nova Tarefa</span>
    </button>

    <!-- Formulário que aparece/desaparece -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="mt-3 overflow-hidden rounded-xl border border-indigo-500/30 bg-neutral-800 shadow-xl"
        style="display: none;"
    >
        <div class="border-b border-neutral-700 bg-neutral-800/80 px-4 py-3">
            <h3 class="text-sm font-semibold text-white">Criar Nova Tarefa</h3>
        </div>

        <form wire:submit.prevent="createTask" class="grid gap-4 p-4 sm:grid-cols-2">
        <!-- Select 1: Projeto -->
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">1. Projeto</label>
            <select wire:model.live="projectId" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Selecione o projeto</option>
                @foreach($projectOptions as $project)
                    <option value="{{ $project['id'] }}">{{ $project['label'] }}</option>
                @endforeach
            </select>
            @error('projectId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Select 2: Estágio (só aparece após selecionar projeto) -->
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">2. Estágio</label>
            <select
                wire:model.live="stageId"
                @disabled(!$projectId)
                class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <option value="">{{ $projectId ? 'Selecione o estágio' : 'Primeiro selecione um projeto' }}</option>
                @foreach($stageOptions as $stage)
                    <option value="{{ $stage['id'] }}">{{ $stage['label'] }}</option>
                @endforeach
            </select>
            @error('stageId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

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

        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Nome da Tarefa</label>
            <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ex: Criar landing page do produto" />
            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">Descrição</label>
            <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Detalhes do escopo, critérios de aceite, links úteis..."></textarea>
            @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

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
            <input type="number" step="0.5" min="0" wire:model="estimatedHours" class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ex: 8" />
            @error('estimatedHours') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Select 3: Tarefa Dependente (só aparece após selecionar estágio) -->
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-300">3. Depende da tarefa (opcional)</label>
            <select
                wire:model="dependentTaskId"
                @disabled(!$stageId)
                class="mt-1 w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <option value="">{{ $stageId ? 'Nenhuma dependência' : 'Primeiro selecione um estágio' }}</option>
                @foreach($dependencyOptions as $taskOption)
                    <option value="{{ $taskOption['id'] }}">{{ $taskOption['label'] }}</option>
                @endforeach
            </select>
            @error('dependentTaskId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2 flex items-center justify-between gap-3 pt-2">
            <button type="button" wire:click="resetFields" class="rounded-lg border border-neutral-600 bg-neutral-900 px-4 py-2 text-sm font-medium text-neutral-300 transition hover:border-neutral-500 hover:bg-neutral-800 hover:text-white">
                Limpar campos
            </button>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-neutral-800">
                Salvar Tarefa
            </button>
        </div>
        </form>
    </div>
</div>
