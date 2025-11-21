<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4">
            <div class="relative w-full max-w-3xl rounded-xl bg-neutral-800 shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-neutral-700 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <svg class="size-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Anexos da Tarefa</h2>
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
                    <!-- Upload Form -->
                    <div class="mb-6 rounded-lg border border-dashed border-neutral-600 bg-neutral-900/50 p-6">
                        <div class="text-center">
                            <svg class="mx-auto mb-3 size-12 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <label class="cursor-pointer">
                                <span class="text-sm text-purple-400 hover:text-purple-300">Clique para selecionar arquivos</span>
                                <span class="text-sm text-neutral-400"> ou arraste e solte</span>
                                <input
                                    type="file"
                                    wire:model="files"
                                    multiple
                                    class="hidden"
                                    accept="*/*"
                                />
                            </label>
                            <p class="mt-1 text-xs text-neutral-500">Fotos, vídeos, documentos, etc. (max 50MB por arquivo)</p>
                        </div>

                        @if(count($files) > 0)
                            <div class="mt-4 space-y-2">
                                <p class="text-xs font-medium text-neutral-300">Arquivos selecionados:</p>
                                @foreach($files as $index => $file)
                                    <div class="flex items-center gap-2 rounded bg-neutral-800 px-3 py-2 text-sm text-neutral-300">
                                        <svg class="size-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>{{ $file->getClientOriginalName() }}</span>
                                        <span class="ml-auto text-xs text-neutral-500">{{ number_format($file->getSize() / 1024, 2) }} KB</span>
                                    </div>
                                @endforeach

                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-medium text-neutral-400">Descrição (opcional)</label>
                                    <input
                                        type="text"
                                        wire:model="description"
                                        placeholder="Ex: Captura de tela do erro..."
                                        class="w-full rounded-lg border border-neutral-600 bg-neutral-900 px-3 py-2 text-sm text-white placeholder:text-neutral-500 focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                    />
                                </div>

                                <div class="mt-3 flex justify-end">
                                    <button
                                        wire:click="uploadFiles"
                                        class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-500"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Enviar Arquivos
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('files.*')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments List -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        @forelse($attachments as $attachment)
                            @php
                                $iconType = $this->getFileIcon($attachment->mime_type, $attachment->extension);
                                $isImage = str_starts_with($attachment->mime_type, 'image/');
                                $isVideo = str_starts_with($attachment->mime_type, 'video/');
                            @endphp
                            <div class="group relative overflow-hidden rounded-lg border border-neutral-700 bg-neutral-900/30 transition hover:bg-neutral-800/50">
                                <!-- Preview area -->
                                <div class="relative flex h-32 items-center justify-center bg-neutral-900">
                                    @if($isImage)
                                        <img
                                            src="{{ asset('storage/attachments/' . $attachment->stored_filename) }}"
                                            alt="{{ $attachment->filename }}"
                                            class="h-full w-full object-cover"
                                        />
                                    @elseif($isVideo)
                                        <div class="flex flex-col items-center text-purple-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Vídeo</span>
                                        </div>
                                    @elseif($iconType === 'pdf')
                                        <div class="flex flex-col items-center text-red-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="mt-1 text-xs">PDF</span>
                                        </div>
                                    @elseif($iconType === 'document')
                                        <div class="flex flex-col items-center text-blue-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Documento</span>
                                        </div>
                                    @elseif($iconType === 'spreadsheet')
                                        <div class="flex flex-col items-center text-green-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Planilha</span>
                                        </div>
                                    @elseif($iconType === 'audio')
                                        <div class="flex flex-col items-center text-yellow-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Áudio</span>
                                        </div>
                                    @elseif($iconType === 'archive')
                                        <div class="flex flex-col items-center text-amber-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Arquivo</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center text-neutral-400">
                                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="mt-1 text-xs">Arquivo</span>
                                        </div>
                                    @endif

                                    <!-- Actions overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/60 opacity-0 transition group-hover:opacity-100">
                                        <a
                                            href="{{ route('attachments.download', $attachment->id) }}"
                                            class="rounded-full bg-purple-600 p-2 text-white transition hover:bg-purple-500"
                                            title="Download"
                                        >
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        @if($attachment->user_id === auth()->id())
                                            <button
                                                wire:click="deleteAttachment({{ $attachment->id }})"
                                                wire:confirm="Tem certeza que deseja excluir este arquivo?"
                                                class="rounded-full bg-red-600 p-2 text-white transition hover:bg-red-500"
                                                title="Excluir"
                                            >
                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- File info -->
                                <div class="p-3">
                                    <p class="truncate text-sm font-medium text-neutral-200" title="{{ $attachment->filename }}">
                                        {{ $attachment->filename }}
                                    </p>

                                    @if($editingAttachmentId === $attachment->id)
                                        <div class="mt-2 space-y-2">
                                            <input
                                                type="text"
                                                wire:model="editingDescription"
                                                placeholder="Descrição..."
                                                class="w-full rounded border border-purple-500/50 bg-neutral-800 px-2 py-1 text-xs text-white focus:border-purple-500 focus:outline-none"
                                            />
                                            <div class="flex justify-end gap-1">
                                                <button
                                                    wire:click="cancelEditing"
                                                    class="rounded px-2 py-1 text-xs text-neutral-400 transition hover:bg-neutral-700"
                                                >
                                                    Cancelar
                                                </button>
                                                <button
                                                    wire:click="updateAttachment"
                                                    class="rounded bg-purple-600 px-2 py-1 text-xs text-white transition hover:bg-purple-500"
                                                >
                                                    Salvar
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        @if($attachment->description)
                                            <p class="mt-1 truncate text-xs text-neutral-400" title="{{ $attachment->description }}">
                                                {{ $attachment->description }}
                                            </p>
                                        @endif

                                        <div class="mt-2 flex items-center justify-between text-xs text-neutral-500">
                                            <span>{{ $attachment->getFormattedSize() }}</span>
                                            <span>{{ $attachment->created_at->diffForHumans() }}</span>
                                        </div>

                                        @if($attachment->user_id === auth()->id())
                                            <button
                                                wire:click="startEditing({{ $attachment->id }})"
                                                class="mt-2 text-xs text-purple-400 transition hover:text-purple-300"
                                            >
                                                Editar descrição
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 flex flex-col items-center justify-center rounded-lg border border-dashed border-neutral-700 py-12 text-center">
                                <svg class="mb-3 size-12 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <p class="text-sm text-neutral-500">Nenhum anexo ainda</p>
                                <p class="text-xs text-neutral-600">Envie arquivos usando o formulário acima</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-700 px-6 py-4">
                    <span class="text-xs text-neutral-500">{{ $attachments->count() }} {{ $attachments->count() === 1 ? 'anexo' : 'anexos' }}</span>
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
