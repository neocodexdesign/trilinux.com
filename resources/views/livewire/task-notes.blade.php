<div x-data="{ showPreview: false, buttonRect: null }">
    <!-- Badge/Ícone para abrir notas -->
    <button
        wire:click="openModal"
        @mouseenter="showPreview = true; buttonRect = $el.getBoundingClientRect()"
        @mouseleave="showPreview = false"
        x-ref="notesButton"
        class="relative inline-flex items-center gap-1 rounded bg-blue-500/20 px-2 py-1 text-xs font-medium text-blue-300 ring-1 ring-blue-500/30 transition-all hover:bg-blue-500/30 active:scale-95"
        title="Notes">
        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        @if($notesCount > 0)
            <span class="text-xs">{{ $notesCount }}</span>
        @endif
    </button>

    <!-- Preview Tooltip - teleportado para fora de toda hierarquia -->
    @if($notesCount > 0)
        <template x-teleport="body">
            <div
                x-show="showPreview"
                x-transition
                @mouseenter="showPreview = true"
                @mouseleave="showPreview = false"
                class="fixed z-[9999] w-96 rounded-lg p-4 shadow-2xl"
                style="display: none; background-color: #575a6f; border: 1px solid rgba(255, 255, 255, 0.2);"
                x-bind:style="buttonRect ? `top: ${buttonRect.bottom + 8}px; left: ${Math.max(8, Math.min(buttonRect.right - 384, window.innerWidth - 384 - 8))}px; background-color: #575a6f; border: 1px solid rgba(255, 255, 255, 0.2);` : 'background-color: #575a6f; border: 1px solid rgba(255, 255, 255, 0.2);'">
                <div class="mb-2 text-xs font-semibold" style="color: #ffffff;">Notes Preview</div>
                <div class="max-h-[400px] space-y-2 overflow-y-auto">
                    @foreach($notes as $note)
                        <div wire:key="preview-note-{{ $note->id }}" class="rounded p-2.5" style="background-color: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="text-xs font-medium" style="color: #ffffff;">{{ $note->user->name }}</span>
                                <span class="text-xs" style="color: #e2e8f0;">{{ $note->created_at->diffForHumans() }}</span>
                                @if($note->is_pinned)
                                    <span class="text-xs">📌</span>
                                @endif
                            </div>
                            <p class="text-xs leading-relaxed" style="color: #e2e8f0;">{{ Str::limit($note->content, 150) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 pt-2 text-xs" style="border-top: 1px solid rgba(255, 255, 255, 0.2); color: #e2e8f0;">Click badge to add or edit notes</div>
            </div>
        </template>
    @endif

    <!-- Modal de Notas - Teleportado para fora de toda hierarquia -->
    @if($showModal)
        <template x-teleport="body">
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-6" style="background-color: rgba(0, 0, 0, 0.7);" wire:click.self="closeModal">
            <div class="flex flex-col rounded-3xl shadow-2xl" style="background: linear-gradient(135deg, #07080cff 0%, #575a6f 100%); max-height: 90vh; width: 900px;" wire:click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between px-8 py-5" style="border-color: rgba(255, 255, 255, 0.2);">
                    <h3 class="text-2xl font-bold" style="color: #d4bcbcff;">
                        📝 {{ $task->name }}
                    </h3>
                    <button wire:click="closeModal" class="rounded-full p-2.5 transition-all hover:scale-110" style="background-color: rgba(255, 255, 255, 0.2); color: white;">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                    <!-- Nova Nota -->
                    <div class="mb-8 rounded-4xl p-6 shadow-xl" style="background: linear-gradient(135deg, #4a67e8ff 0%, #575a6f 100%); border: 1px solid #272424ff;">
                        <label class="mb-4 block text-xl font-black" style="color: #ffffff;">✨ Nova Nota</label>
                        <textarea
                            wire:model="newNoteContent"
                            rows="5"
                            class="rounded-xl border-1 px-5 py-4 text-lg shadow-lg"
                            style="background-color: #d4bcbcff; color: #1e293b; border-color: #e0e7ff; width: 100%;"
                            placeholder="Escreva algo incrível..."
                            autofocus></textarea>
                        @error('newNoteContent')
                            <span class="mt-2 block text-sm font-bold" style="color: #fef2f2; background-color: #dc2626; padding: 8px 12px; border-radius: 8px;">{{ $message }}</span>
                        @enderror

                        <!-- Upload de Anexos -->
                        <div class="mt-4">
                            <label class="mb-2 block text-base font-bold" style="color: #ffffff;">📎 Anexar Arquivos</label>
                            <input
                                type="file"
                                wire:model="attachments"
                                multiple
                                class="block w-full text-sm rounded-lg cursor-pointer"
                                style="background-color: rgba(255, 255, 255, 0.1); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 8px;">
                            @error('attachments.*')
                                <span class="mt-2 block text-sm font-bold" style="color: #fef2f2; background-color: #dc2626; padding: 8px 12px; border-radius: 8px;">{{ $message }}</span>
                            @enderror
                            <div wire:loading wire:target="attachments" class="mt-2 text-sm" style="color: #ffffff;">
                                ⏳ Carregando arquivos...
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button
                                wire:click="createNote"
                                class="rounded-xl px-8 py-3.5 text-lg font-black shadow-xl transition-all hover:scale-105"
                                style="background-color: #ffffff; color: #667eea;">
                                💾 Salvar Nota
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Notas -->
                    @if($notes->count() > 0)
                        <div class="space-y-5">
                            @foreach($notes as $note)
                                <div wire:key="note-{{ $note->id }}" class="rounded-2xl p-6 shadow-2xl transform transition-all hover:scale-102" style="background-color: #575a6f; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <div class="mb-3 flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="text-base font-bold" style="color: #ffffff;">
                                                👤 {{ $note->user->name }}
                                            </span>
                                            <span class="text-sm font-medium" style="color: #e2e8f0;">
                                                {{ $note->created_at->format('d/m/Y H:i') }}
                                            </span>
                                            @if($note->is_pinned)
                                                <span class="text-xl">📌</span>
                                            @endif
                                        </div>

                                        @if($note->user_id === auth()->id())
                                            <div class="flex gap-3">
                                                <button
                                                    wire:click="togglePin({{ $note->id }})"
                                                    class="text-2xl transition-all hover:scale-125"
                                                    title="{{ $note->is_pinned ? 'Desafixar' : 'Fixar' }}">
                                                    {{ $note->is_pinned ? '📌' : '📍' }}
                                                </button>
                                                <button
                                                    wire:click="editNote({{ $note->id }})"
                                                    class="text-2xl transition-all hover:scale-125"
                                                    title="Editar">
                                                    ✏️
                                                </button>
                                                <button
                                                    wire:click="deleteNote({{ $note->id }})"
                                                    wire:confirm="Tem certeza que quer deletar esta nota?"
                                                    class="text-2xl transition-all hover:scale-125"
                                                    title="Deletar">
                                                    🗑️
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    @if($editingNoteId === $note->id)
                                        <div>
                                            <textarea
                                                wire:model="editingContent"
                                                rows="4"
                                                class="rounded-xl border px-5 py-4 text-lg shadow-lg"
                                                style="background-color: #ffffff; color: #1e293b; border-color: rgba(255, 255, 255, 0.3); width: 100%;"></textarea>
                                            @error('editingContent')
                                                <span class="mt-2 block text-sm font-bold" style="color: #fef2f2; background-color: #dc2626; padding: 8px 12px; border-radius: 8px;">{{ $message }}</span>
                                            @enderror
                                            <div class="mt-4 flex gap-3">
                                                <button
                                                    wire:click="updateNote"
                                                    class="rounded-xl px-6 py-3 text-base font-black shadow-xl transition-all hover:scale-105"
                                                    style="background-color: #10b981; color: white;">
                                                    ✅ Salvar
                                                </button>
                                                <button
                                                    wire:click="cancelEdit"
                                                    class="rounded-xl px-6 py-3 text-base font-black shadow-xl transition-all hover:scale-105"
                                                    style="background-color: #6b7280; color: white;">
                                                    ❌ Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <p class="whitespace-pre-wrap text-xl leading-relaxed font-medium" style="color: #ffffff;">{{ $note->content }}</p>

                                        <!-- Anexos -->
                                        @if($note->attachments->count() > 0)
                                            <div class="mt-4 flex flex-wrap gap-3">
                                                @foreach($note->attachments as $attachment)
                                                    <div class="flex items-center gap-2 rounded-lg px-3 py-2 transition-all hover:scale-105" style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="flex items-center gap-2 text-decoration-none">
                                                            <span class="text-2xl">{{ $attachment->icon }}</span>
                                                            <div class="flex flex-col">
                                                                <span class="text-sm font-medium" style="color: #ffffff;">{{ Str::limit($attachment->filename, 20) }}</span>
                                                                <span class="text-xs" style="color: #e2e8f0;">{{ $attachment->formatted_size }}</span>
                                                            </div>
                                                        </a>
                                                        @if($note->user_id === auth()->id())
                                                            <button
                                                                wire:click="deleteAttachment({{ $attachment->id }})"
                                                                wire:confirm="Deletar este arquivo?"
                                                                class="text-lg transition-all hover:scale-125 ml-2"
                                                                title="Deletar arquivo">
                                                                ❌
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                
            </div>
        </div>
        </template>
    @endif
</div>
