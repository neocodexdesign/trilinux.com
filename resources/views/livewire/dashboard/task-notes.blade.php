<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4">
            <div class="relative w-full max-w-2xl rounded-xl bg-neutral-800 shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <svg class="size-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Notas da Tarefa</h2>
                            <p class="text-xs text-neutral-400">{{ $taskName }}</p>
                        </div>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="rounded-lg p-2 text-neutral-400 transition hover:bg-neutral-700 hover:text-white"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="max-h-[70vh] overflow-y-auto p-6">
                    <!-- New Note Form -->
                    <div class="mb-6 rounded-lg border border-neutral-700 bg-neutral-900/50 p-4">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-300">
                            Nova Nota
                        </label>
                        <textarea
                            wire:model="newNoteContent"
                            rows="3"
                            placeholder="Escreva sua nota aqui..."
                            class="w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                        ></textarea>
                        <div class="mt-3 flex justify-end">
                            <button
                                wire:click="createNote"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-500"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar Nota
                            </button>
                        </div>
                    </div>

                    <!-- Notes List -->
                    <div class="space-y-4">
                        @forelse($notes as $note)
                            <div class="group relative rounded-lg border {{ $note->is_pinned ? 'border-amber-500/50 bg-amber-950/20' : 'border-neutral-700 bg-neutral-900/30' }} p-4 transition hover:bg-neutral-800/50">
                                <!-- Pin indicator -->
                                @if($note->is_pinned)
                                    <div class="absolute -top-2 -right-2 rounded-full bg-amber-500 p-1">
                                        <svg class="size-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Note Header -->
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-neutral-200">{{ $note->user?->name ?? 'Usuário' }}</span>
                                        <span class="text-xs text-neutral-500">{{ $note->created_at->diffForHumans() }}</span>
                                        @if($note->created_at->ne($note->updated_at))
                                            <span class="text-xs text-neutral-500">(editado)</span>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    @if($note->user_id === auth()->id())
                                        <div class="flex items-center gap-1 opacity-0 transition group-hover:opacity-100">
                                            <button
                                                wire:click="togglePin({{ $note->id }})"
                                                class="rounded p-1 text-neutral-400 transition hover:bg-neutral-700 hover:text-amber-400"
                                                title="{{ $note->is_pinned ? 'Desafixar' : 'Fixar' }}"
                                            >
                                                <svg class="size-4" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="startEditing({{ $note->id }})"
                                                class="rounded p-1 text-neutral-400 transition hover:bg-neutral-700 hover:text-blue-400"
                                                title="Editar"
                                            >
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="deleteNote({{ $note->id }})"
                                                wire:confirm="Tem certeza que deseja excluir esta nota?"
                                                class="rounded p-1 text-neutral-400 transition hover:bg-neutral-700 hover:text-red-400"
                                                title="Excluir"
                                            >
                                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Note Content -->
                                @if($editingNoteId === $note->id)
                                    <div class="space-y-3">
                                        <textarea
                                            wire:model="editingNoteContent"
                                            rows="3"
                                            class="w-full rounded-lg border border-blue-500/50 bg-neutral-900 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        ></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button
                                                wire:click="cancelEditing"
                                                class="rounded-lg border border-neutral-600 px-3 py-1.5 text-xs font-medium text-neutral-300 transition hover:bg-neutral-700"
                                            >
                                                Cancelar
                                            </button>
                                            <button
                                                wire:click="updateNote"
                                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-blue-500"
                                            >
                                                Salvar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <p class="whitespace-pre-wrap text-sm text-neutral-300">{{ $note->content }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center rounded-lg border border-dashed border-neutral-700 py-12 text-center">
                                <svg class="mb-3 size-12 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <p class="text-sm text-neutral-500">Nenhuma nota ainda</p>
                                <p class="text-xs text-neutral-600">Adicione a primeira nota acima</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-700 px-6 py-4">
                    <span class="text-xs text-neutral-500">{{ $notes->count() }} {{ $notes->count() === 1 ? 'nota' : 'notas' }}</span>
                    <button
                        wire:click="closeModal"
                        class="rounded-lg border border-neutral-600 bg-neutral-900 px-4 py-2 text-sm font-medium text-neutral-300 transition hover:border-neutral-500 hover:bg-neutral-800 hover:text-white"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
