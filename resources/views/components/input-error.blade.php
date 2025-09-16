@props(['for'])

@error($for)
<div {{ $attributes->merge(['class' => 'invalid-feedback d-block small']) }}>
    {{ $message }}
</div>
@enderror
