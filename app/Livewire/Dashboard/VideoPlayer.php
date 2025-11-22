<?php

namespace App\Livewire\Dashboard;

use App\Models\Attachment;
use Livewire\Attributes\On;
use Livewire\Component;

class VideoPlayer extends Component
{
    public bool $showModal = false;
    public ?int $attachmentId = null;
    public ?string $videoUrl = null;
    public ?string $videoTitle = null;

    #[On('play-video')]
    public function playVideo($attachmentId)
    {
        $attachment = Attachment::find($attachmentId);

        if (!$attachment || !str_starts_with($attachment->mime_type, 'video/')) {
            return;
        }

        $this->attachmentId = $attachmentId;
        $this->videoUrl = asset('storage/attachments/' . $attachment->stored_filename);
        $this->videoTitle = $attachment->filename;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->attachmentId = null;
        $this->videoUrl = null;
        $this->videoTitle = null;
    }

    public function render()
    {
        return view('livewire.dashboard.video-player');
    }
}
