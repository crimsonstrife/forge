@props([
    'color' => '#607D8B',
    'icon' => 'filter_none',
    'label' => null,
])

<span class="issue-tier-badge" style="--tier-color: {{ $color }}">
    <i class="material-icons md-14">{{ $icon }}</i>
    @if($label)
        <span class="label">{{ $label }}</span>
    @endif
</span>

<style>
    .issue-tier-badge{
        display:inline-flex;align-items:center;gap:.375rem;
        padding:.125rem .5rem;border-radius:9999px;
        background: color-mix(in oklab, var(--tier-color) 14%, transparent);
        border:1px solid color-mix(in oklab, var(--tier-color) 32%, #0000);
        color: var(--tier-color);
        font-size: .75rem; line-height: 1rem; font-weight: 600;
    }
    .issue-tier-badge .material-icons.md-14{font-size:14px;line-height:14px}
</style>
