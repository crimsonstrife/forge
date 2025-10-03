<button type="button"
        class="btn btn-sm {{ $isNext ? 'btn-warning' : 'btn-outline-secondary' }}"
        title="{{ $isNext ? 'Unpin from Next' : 'Pin to Next' }}"
        wire:click="toggle">
    {{-- simple star icon using wa-icon; fallback to ★/☆ --}}
    @if(function_exists('wa_icon'))
        <wa-icon family="{{ $isNext ? 'solid' : 'outline' }}" name="{{ $isNext ? 'star' : 'star' }}"></wa-icon>
    @else
        {!! $isNext ? '★' : '☆' !!}
    @endif
</button>
