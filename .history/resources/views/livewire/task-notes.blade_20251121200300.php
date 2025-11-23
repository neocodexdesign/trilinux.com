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
    <button
        wire:click="openModal"
        title="Notas da tarefa"
        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/30 hover:bg-white/10"
    >
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Notas
        @if($notesCount > 0)
            <span class="text-xs text-white/70">{{ $notesCount }}</span>
        @endif
    </button>

    @if($showModal)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-4"
                wire:click.self="closeModal"
            >
                <div
                    class="relative w-full max-w-6xl rounded-[32px] border border-white/10 bg-gradient-to-br from-[#05070f] to-[#0c1226] shadow-2xl outline outline-1 outline-[#1a213c]"
                    wire:click.stop
                >
                    <div class="absolute inset-x-6 top-0 h-1 rounded-t-[32px] bg-red-500"></div>
                    <div class="flex flex-col gap-6 px-8 py-6">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.5em] text-stone-400">Notas</p>
                                <h3 class="text-3xl font-black text-white">Notas da Tarefa</h3>
                                <p class="text-sm text-white/60">Documente insights rápidos e contextos importantes para {{ $task->name }}</p>
                            </div>
                            <button
                                wire:click="closeModal"
                                class="rounded-full border border-white/20 bg-white/5 p-3 text-white transition hover:border-white/40 hover:bg-white/10"
                            >
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="rounded-[30px] border border-white/10 bg-white/5 p-6 shadow-inner backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-white/70 tracking-[0.4em]">Nova nota</p>
                            <textarea
                                wire:model.defer="newNoteContent"
                                rows="4"
                                class="mt-3 w-full rounded-[20px] border border-white/10 bg-black/60 px-4 py-3 text-base text-white placeholder-white/40"
                                placeholder="Escreva sua nota aqui..."
                            ></textarea>
                            @error('newNoteContent')
                                <span class="mt-2 block text-xs font-semibold text-rose-400">{{ $message }}</span>
                            @enderror

                            <div class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold uppercase tracking-[0.4em] text-white/60">Anexos</label>
                                    <input
                                        type="file"
                                        wire:model="attachments"
                                        multiple
                                        class="w-full rounded-[12px] border border-white/15 bg-black/40 px-3 py-2 text-sm text-white shadow-inner"
                                    >
                                    @error('attachments.*')
                                        <span class="text-xs font-semibold text-rose-400">{{ $message }}</span>
                                    @enderror
                                    <div wire:loading wire:target="attachments" class="text-xs text-white/60">📎 Carregando arquivos...</div>
                                </div>

                                <button
                                    wire:click="createNote"
                                    class="ml-auto rounded-[16px] bg-amber-500 px-6 py-3 text-sm font-extrabold uppercase tracking-widest text-white shadow-lg transition hover:bg-amber-400"
                                >
                                    + Adicionar Nota
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-white">Notas importantes</h4>
                            <span class="text-xs font-semibold uppercase tracking-[0.4em] text-white/50">{{ $notesCount }} registros</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @forelse($notes as $index => $note)
                                @php
                                    $palette = $notePalette[$index % count($notePalette)];
                                @endphp
                                <div
                                    wire:key="note-{{ $note->id }}"
                                    class="relative rounded-[26px] border p-5 shadow-[0_25px_50px_rgba(0,0,0,0.55)] transition-transform duration-300 hover:-translate-y-1"
                                    style="background: {{ $palette['gradient'] }}; border-color: {{ $palette['border'] }};"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-[0.5em] text-white/70">Notas importantes</p>
                                            <p class="text-sm font-bold text-white">{{ $note->user->name }}</p>
                                            <p class="text-[11px] text-white/70">{{ $note->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($note->is_pinned)
                                                <span class="text-lg" title="Pinned">⭐</span>
                                            @endif
                                            @if($note->user_id === auth()->id())
                                                <button
                                                    wire:click="togglePin({{ $note->id }})"
                                                    class="rounded-full bg-white/10 p-1 text-xs text-white transition hover:bg-white/20"
                                                    title="{{ $note->is_pinned ? 'Desafixar' : 'Fixar' }}"
                                                >
                                                    {{ $note->is_pinned ? '📌' : '📍' }}
                                                </button>
                                                <button
                                                    wire:click="editNote({{ $note->id }})"
                                                    class="rounded-full bg-white/10 p-1 text-xs text-white transition hover:bg-white/20"
                                                    title="Editar"
                                                >
                                                    ✏️
                                                </button>
                                                <button
                                                    wire:click="deleteNote({{ $note->id }})"
                                                    wire:confirm="Tem certeza que quer deletar esta nota?"
                                                    class="rounded-full bg-white/10 p-1 text-xs text-white transition hover:bg-white/20"
                                                    title="Excluir"
                                                >
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-4 min-h-[120px]">
                                        @if($editingNoteId === $note->id)
                                            <textarea
                                                wire:model.defer="editingContent"
                                                rows="5"
                                                class="w-full rounded-[18px] border border-white/30 bg-white/80 px-4 py-3 text-sm text-slate-900"
                                            ></textarea>
                                            @error('editingContent')
                                                <span class="mt-2 block text-xs font-semibold text-rose-400">{{ $message }}</span>
                                            @enderror
                                            <div class="mt-3 flex gap-2">
                                                <button
                                                    wire:click="updateNote"
                                                    class="rounded-[16px] bg-emerald-500 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white"
                                                >
                                                    Salvar
                                                </button>
                                                <button
                                                    wire:click="cancelEdit"
                                                    class="rounded-[16px] border border-white/30 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white/80"
                                                >
                                                    Cancelar
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-sm leading-relaxed text-white/90 whitespace-pre-line">{{ $note->content }}</p>
                                        @endif
                                    </div>

                                    @if($note->attachments->count() > 0)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach($note->attachments as $attachment)
                                                <div class="flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs uppercase tracking-[0.2em] text-white/80">
                                                    <span>📎</span>
                                                    <span class="truncate">{{ Str::limit($attachment->filename, 20) }}</span>
                                                    @if($note->user_id === auth()->id())
                                                        <button
                                                            wire:click="deleteAttachment({{ $attachment->id }})"
                                                            wire:confirm="Deletar este arquivo?"
                                                            class="text-white/80 transition hover:text-white"
                                                            title="Remover anexo"
                                                        >
                                                            ✖
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-full rounded-[26px] border border-white/10 bg-white/5 px-6 py-8 text-center text-sm text-white/60">
                                    Ainda não existem notas registradas.
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-between border-t border-white/10 pt-4 text-xs text-white/50">
                            <span>{{ $notesCount }} notas no total</span>
                            <button
                                wire:click="closeModal"
                                class="rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/40"
                            >
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
