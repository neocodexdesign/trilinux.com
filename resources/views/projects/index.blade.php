<x-layouts.app :title="__('Projects')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Projetos</h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-400">Todos os projetos disponíveis.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Responsável</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($projects as $project)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $project->name }}</div>
                                    @if($project->description)
                                        <div class="mt-0.5 line-clamp-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $project->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $project->status === 'completed' ? 'bg-green-500/20 text-green-300' : ($project->status === 'in_progress' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300') }}">
                                        {{ match($project->status) {
                                            'planned' => 'Planejado',
                                            'in_progress' => 'Em Progresso',
                                            'paused' => 'Pausado',
                                            'completed' => 'Completado',
                                            'cancelled' => 'Cancelado',
                                            default => $project->status
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-200">
                                    @if($project->responsible)
                                        {{ $project->responsible->name }}
                                    @elseif($project->team)
                                        {{ $project->team->name }}
                                    @else
                                        <span class="text-zinc-500 dark:text-zinc-400">Não atribuído</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a
                                        href="{{ route('projects.manage', $project) }}"
                                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500"
                                        wire:navigate
                                    >
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                    Nenhum projeto encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($projects, 'links'))
                <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

