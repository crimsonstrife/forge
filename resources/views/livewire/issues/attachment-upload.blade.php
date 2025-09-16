<div class="d-flex align-items-center gap-2">
    <input type="file" wire:model="files" multiple class="form-control form-control-sm" style="max-width: 320px;">
    <button wire:click="save" class="btn btn-primary btn-sm" @disabled(empty($files))>Upload</button>
    @error('files.*') <span class="small text-danger">{{ $message }}</span> @enderror
</div>
