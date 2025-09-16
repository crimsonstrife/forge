<form action="{!! $url !!}" method="POST" {!! $attributes !!}>
    @csrf
    <wa-button type="submit" variant="brand" aria-label="{{ $label }}">
        <span class="{!! $basename !!}__label">{{ $label }}</span>
    </wa-button>
</form>
