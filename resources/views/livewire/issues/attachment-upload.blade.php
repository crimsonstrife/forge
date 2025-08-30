<div class="flex items-center gap-2">
    <input type="file" wire:model="files" multiple class="text-sm">
    <button wire:click="save"
            class="rounded-lg px-3 py-1.5 bg-primary-600 text-white text-sm hover:bg-primary-700 disabled:opacity-50"
        @disabled(empty($files))>
        Upload
    </button>
    @error('files.*') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
</div>
