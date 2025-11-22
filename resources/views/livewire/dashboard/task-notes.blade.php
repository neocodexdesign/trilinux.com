@php
    use Illuminate\Support\Str;

    $notePalette = [
        ['gradient' => 'linear-gradient(135deg,#c38b22,#f6d365)', 'border' => '#fbbf24'],
        ['gradient' => 'linear-gradient(135deg,#8b5cf6,#d946ef)', 'border' => '#a855f7'],
        ['gradient' => 'linear-gradient(135deg,#16a34a,#4ade80)', 'border' => '#22c55e'],
        ['gradient' => 'linear-gradient(135deg,#ec4899,#f472b6)', 'border' => '#db2777'],
        ['gradient' => 'linear-gradient(135deg,#0ea5e9,#22d3ee)', 'border' => '#0284c7'],
        ['gradient' => 'linear-gradient(135deg,#f97316,#fdba74)', 'border' => '#fb923c'],
    ];
@endphp

<div>
    @if($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-4"
            wire:click.self="closeModal"
            x-data
            @keydown.escape.window="$wire.closeModal()"
        >
            <div
                class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[32px] border border-white/10 bg-gradient-to-br from-[#05070f] to-[#0c1226] shadow-2xl outline outline-1 outline-[#1a213c]"
                wire:click.stop
            >
                <!-- Top accent bar -->
                <div class="absolute inset-x-6 top-0 h-1 rounded-t-[32px] bg-amber-500"></div>

                <!-- Scrollable content -->
                <div class="flex flex-col gap-6 px-6 py-6 max-h-[90vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.5em] text-stone-400">Notas</p>
                            <h3 class="text-2xl font-black text-white">Notas da Tarefa</h3>
                            <p class="text-sm text-white/60">{{ $taskName }}</p>
                        </div>
                        <button
                            wire:click="closeModal"
                            class="flex items-center justify-center size-10 rounded-lg bg-red-600 text-white transition hover:bg-red-500"
                            title="Fechar"
                        >
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- New Note Form -->
                    <div class="rounded-[24px] border border-white/10 bg-white/5 p-5 shadow-inner backdrop-blur">
                        <p class="text-xs font-semibold uppercase text-white/70 tracking-[0.4em]">Nova nota</p>
                        <textarea
                            wire:model="newNoteContent"
                            rows="3"
                            class="mt-3 w-full rounded-[16px] border border-white/10 bg-black/60 px-4 py-3 text-base text-white placeholder-white/40 focus:border-amber-500 focus:outline-none"
                            placeholder="Escreva sua nota aqui..."
                        ></textarea>

                        <div class="mt-4 flex justify-end">
                            <button
                                wire:click="createNote"
                                class="rounded-[12px] bg-amber-500 px-5 py-2.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg transition hover:bg-amber-400"
                            >
                                + Adicionar Nota
                            </button>
                        </div>
                    </div>

                    <!-- Notes Header -->
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-white">Notas registradas</h4>
                        <span class="text-xs font-semibold uppercase tracking-[0.4em] text-white/50">{{ $notes->count() }} {{ $notes->count() === 1 ? 'registro' : 'registros' }}</span>
                    </div>

                    <!-- Notes Grid -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        @forelse($notes as $index => $note)
                            @php
                                $palette = $notePalette[$index % count($notePalette)];
                            @endphp
                            <div
                                wire:key="note-{{ $note->id }}"
                                class="relative rounded-[20px] border p-4 shadow-[0_15px_30px_rgba(0,0,0,0.4)] transition-transform duration-300 hover:-translate-y-1"
                                style="background: {{ $palette['gradient'] }}; border-color: {{ $palette['border'] }};"
                            >
                                <!-- Note Header -->
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $note->user->name }}</p>
                                        <p class="text-[11px] text-white/70">{{ $note->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if($note->is_pinned)
                                            <span class="text-lg" title="Fixada">&#11088;</span>
                                        @endif
                                        @if($note->user_id === auth()->id())
                                            <button
                                                wire:click="togglePin({{ $note->id }})"
                                                class="rounded-full bg-white/20 p-1.5 text-white transition hover:bg-white/30"
                                                title="{{ $note->is_pinned ? 'Desafixar' : 'Fixar' }}"
                                            >
                                                <svg class="size-3.5" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="startEditing({{ $note->id }})"
                                                class="rounded-full bg-white/20 p-1.5 text-white transition hover:bg-white/30"
                                                title="Editar"
                                            >
                                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="deleteNote({{ $note->id }})"
                                                wire:confirm="Tem certeza que deseja excluir esta nota?"
                                                class="rounded-full bg-white/20 p-1.5 text-white transition hover:bg-white/30"
                                                title="Excluir"
                                            >
                                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Note Content -->
                                <div class="mt-3 min-h-[80px]">
                                    @if($editingNoteId === $note->id)
                                        <textarea
                                            wire:model="editingNoteContent"
                                            rows="4"
                                            class="w-full rounded-[12px] border border-white/30 bg-white/90 px-3 py-2 text-sm text-slate-900 focus:outline-none"
                                        ></textarea>
                                        <div class="mt-2 flex gap-2">
                                            <button
                                                wire:click="updateNote"
                                                class="rounded-[10px] bg-emerald-500 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-emerald-400"
                                            >
                                                Salvar
                                            </button>
                                            <button
                                                wire:click="cancelEditing"
                                                class="rounded-[10px] border border-white/30 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-white/80 transition hover:bg-white/10"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-sm leading-relaxed text-white/90 whitespace-pre-line">{{ $note->content }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-[20px] border border-white/10 bg-white/5 px-6 py-10 text-center">
                                <svg class="mx-auto mb-3 size-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <p class="text-sm text-white/50">Nenhuma nota ainda</p>
                                <p class="text-xs text-white/30">Adicione a primeira nota acima</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between border-t border-white/10 pt-4">
                        <span class="text-xs text-white/50">{{ $notes->count() }} {{ $notes->count() === 1 ? 'nota' : 'notas' }} no total</span>
                        <button
                            wire:click="closeModal"
                            class="rounded-[12px] border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/80 transition hover:border-white/40 hover:bg-white/5"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
