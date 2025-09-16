@props([
    'action',
    'confirm' => __('Are you sure? This action cannot be undone.'),
    'label' => __('Delete'),
])

<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm));" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        {{ $label }}
    </button>
</form>
