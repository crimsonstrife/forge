<div class="d-inline-flex gap-2">
    @if ($isRunning)
        <button type="button" class="btn btn-warning btn-sm" wire:click="stop">Stop</button>
    @else
        <button type="button" class="btn btn-primary btn-sm" wire:click="start">Start</button>
    @endif
</div>
