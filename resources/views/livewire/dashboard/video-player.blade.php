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
                        class="w-full"
                        src="{{ $videoUrl }}"
                    >
                        Seu navegador não suporta a reprodução de vídeo.
                    </video>
                </div>
            </div>
        </div>
    @endif
</div>
