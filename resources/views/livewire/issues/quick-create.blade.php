<div class="d-flex align-items-center gap-2">
    <input type="text" class="form-control" placeholder="Quick issue summary…" wire:model.defer="summary" wire:keydown.enter="save">
    <button class="btn btn-primary" wire:click="save">Add</button>
    <a href="{{ route('issues.create', ['project'=>$project]) }}" class="small link-secondary">Advanced…</a>
</div>
<x-input-error for="summary" class="mt-2"/>
