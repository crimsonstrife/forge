@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger', 'role' => 'alert', 'aria-live' => 'assertive']) }}>
        <p class="fw-medium mb-2">{{ __('Whoops! Something went wrong.') }}</p>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
