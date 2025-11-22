<div>
    @if($showModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4"
             x-data
             @keydown.escape.window="$wire.closeModal()">
            <div class="relative w-full max-w-4xl">
                <!-- Close button -->
                <button
                    wire:click="closeModal"
                    class="absolute -top-12 right-0 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                >
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Video title -->
                <h3 class="mb-3 text-lg font-medium text-white truncate">{{ $videoTitle }}</h3>

                <!-- Video player -->
                <div class="overflow-hidden rounded-xl bg-black shadow-2xl">
                    <video
                        controls
                        autoplay
                        playsinline
                        class="w-full"
                        x-ref="videoPlayer"
                        x-on:error="$refs.videoError.classList.remove('hidden')"
                    >
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        <source src="{{ $videoUrl }}" type="video/quicktime">
                        <source src="{{ $videoUrl }}" type="video/webm">
                        Seu navegador não suporta a reprodução de vídeo.
                    </video>

                    <!-- Error message with download option -->
                    <div x-ref="videoError" class="hidden p-8 text-center">
                        <svg class="mx-auto size-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-white mb-4">Não foi possível reproduzir o vídeo no navegador.</p>
                        <p class="text-gray-400 text-sm mb-4">O formato do vídeo pode não ser compatível. Tente baixar o arquivo.</p>
                        <a href="{{ route('attachments.download', $attachmentId) }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-500 transition">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Baixar Vídeo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
