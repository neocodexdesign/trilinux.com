<div class="flex w-full flex-col gap-4">
    <div>
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Markdown Plans</h1>
        <p class="text-sm text-neutral-600 dark:text-neutral-400">Gerencie seus planos e reintegre no projeto quando precisar.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Projeto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Atualizado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Gerado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($plans as $plan)
                        @php
                            $project = $plan->notable;
                            $stagesCnt = (int) ($stageCounts[$plan->id] ?? 0);
                            $tasksCnt = (int) ($taskCounts[$plan->id] ?? 0);
                        @endphp

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $project?->name ?? 'Projeto removido' }}</div>
                                <div class="mt-0.5 text-xs text-zinc-600 dark:text-zinc-400">
                                    Por {{ $plan->user?->name ?? 'Usuário' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-200">
                                {{ $plan->updated_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-200">
                                {{ $stagesCnt }} etapas / {{ $tasksCnt }} tarefas
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="open({{ $plan->id }})"
                                        class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                    >
                                        Abrir
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="reintegrate({{ $plan->id }})"
                                        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500"
                                    >
                                        Reintegrar
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="rebuildChecklist({{ $plan->id }})"
                                        class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-700 shadow-sm transition hover:bg-emerald-500/15 dark:text-emerald-300"
                                    >
                                        Refazer checklist
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="purgeGenerated({{ $plan->id }})"
                                        class="rounded-lg border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-sm font-medium text-amber-700 shadow-sm transition hover:bg-amber-500/15 dark:text-amber-300"
                                    >
                                        Excluir gerados
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="deletePlan({{ $plan->id }})"
                                        class="rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm font-medium text-red-700 shadow-sm transition hover:bg-red-500/15 dark:text-red-300"
                                    >
                                        Excluir plano
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                Nenhum markdown plan encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
            {{ $plans->links() }}
        </div>
    </div>
</div>

