@props(['task'])

@php
    $media = $task->getMediaSummary();
    $firstVideo = $task->getFirstVideo();
@endphp

@if($media['total'] > 0)
    <div class="flex items-center gap-1.5" @click.stop>
        @if($media['videos'] > 0)
            <button
                wire:click.stop="$dispatch('play-video', { attachmentId: {{ $firstVideo?->id }} })"
                class="flex items-center gap-1 rounded-full bg-red-500/20 px-2 py-1 text-red-300 transition hover:bg-red-500/40"
                title="Reproduzir vídeo">
                <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <span class="text-[10px] font-medium">{{ $media['videos'] }}</span>
            </button>
        @endif
        @if($media['images'] > 0)
            <button
                wire:click.stop="$dispatch('open-task-attachments', { taskId: {{ $task->id }} })"
                class="flex items-center gap-1 rounded-full bg-green-500/20 px-2 py-1 text-green-300 transition hover:bg-green-500/40"
                title="Ver fotos">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[10px] font-medium">{{ $media['images'] }}</span>
            </button>
        @endif
        @if($media['documents'] > 0)
            <button
                wire:click.stop="$dispatch('open-task-attachments', { taskId: {{ $task->id }} })"
                class="flex items-center gap-1 rounded-full bg-blue-500/20 px-2 py-1 text-blue-300 transition hover:bg-blue-500/40"
                title="Ver documentos">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[10px] font-medium">{{ $media['documents'] }}</span>
            </button>
        @endif
    </div>
@endif
