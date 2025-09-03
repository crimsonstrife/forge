@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success py-2 small mb-3']) }} role="alert">
        {{ $status }}
    </div>
@endif
