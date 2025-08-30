<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class AttachmentUpload extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public Issue $issue;

    /** @var array<int,TemporaryUploadedFile> */
    #[Validate(['files.*' => 'file|max:10240'])] // 10MB
    public array $files = [];

    public function mount(Issue $issue): void
    {
        $this->authorize('update', $issue);
        $this->issue = $issue;
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function save(): void
    {
        $this->authorize('update', $this->issue);
        $this->validate();

        foreach ($this->files as $file) {
            $this->issue
                ->addMedia($file->getRealPath())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('attachments');
        }

        $this->reset('files');
        $this->dispatch('attachments-updated');
        $this->dispatch('notify', title: 'Uploaded', body: 'Attachment(s) added.');
    }

    public function render(): View
    {
        return view('livewire.issues.attachment-upload');
    }
}

