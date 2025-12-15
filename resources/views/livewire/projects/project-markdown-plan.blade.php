<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Markdown Plan</h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                {{ $project->name }}
            </p>
        </div>

        <a
            href="{{ route('projects.manage', $project) }}"
            class="inline-flex items-center rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800"
            wire:navigate
        >
            Voltar ao Projeto
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Markdown</h2>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-800 disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200"
                    >
                        Save
                    </button>
                    <button
                        type="button"
                        wire:click="export"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50"
                    >
                        Export → Projeto
                    </button>
                </div>
            </div>

            <textarea
                wire:model.defer="markdown"
                class="min-h-[520px] w-full rounded-lg border border-neutral-300 bg-neutral-50 p-3 font-mono text-sm text-neutral-900 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                placeholder="# Checklist&#10;&#10;## Etapa&#10;- [ ] Tarefa"
            ></textarea>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Checklist Vivo</h2>
                    <button
                        type="button"
                        wire:click="refreshGenerated"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 disabled:opacity-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800"
                    >
                        Recalcular
                    </button>
                </div>

                @if($stages->count() === 0)
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        Nenhuma etapa exportada ainda. Use “Export → Projeto” para gerar as etapas e tarefas.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach($stages as $stage)
                            <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-950">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $stage->name }}</h3>
                                    <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $stage->status === 'completed' ? 'bg-green-500/20 text-green-300' : ($stage->status === 'in_progress' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300') }}">
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

                                <div class="space-y-2">
                                    @foreach($stage->tasks as $task)
                                        <label class="flex cursor-pointer items-start gap-3 rounded-md px-2 py-1.5 hover:bg-neutral-100 dark:hover:bg-neutral-900">
                                            <input
                                                type="checkbox"
                                                class="mt-0.5 size-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-700"
                                                @checked($task->status === 'completed')
                                                wire:click="toggle({{ $task->id }})"
                                                wire:loading.attr="disabled"
                                            />
                                            <div class="flex-1">
                                                <div class="text-sm text-neutral-900 dark:text-neutral-100 {{ $task->status === 'completed' ? 'line-through opacity-70' : '' }}">
                                                    {{ $task->name }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-3">
                    <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Markdown Gerado</h2>
                    <p class="text-xs text-neutral-600 dark:text-neutral-400">Reconstruído a partir do status real das tasks.</p>
                </div>

                <textarea
                    readonly
                    class="min-h-[240px] w-full rounded-lg border border-neutral-300 bg-neutral-50 p-3 font-mono text-sm text-neutral-900 shadow-inner dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100"
                >{{ $generated }}</textarea>
            </div>
        </div>
    </div>
</div>

