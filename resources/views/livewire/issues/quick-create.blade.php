<div class="flex items-center gap-3">
    <x-input type="text" class="w-full" placeholder="Quick issue summary…" wire:model.defer="summary"
             wire:keydown.enter="save" />
    <x-button wire:click="save">Add</x-button>
    <a href="{{ route('issues.create', ['project'=>$project]) }}" class="text-sm text-gray-600 hover:underline">
        Advanced…
    </a>
</div>
<x-input-error for="summary" class="mt-2"/>
