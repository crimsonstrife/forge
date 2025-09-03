@props([
  'align' => 'right',
  'width' => 'auto',
  'contentClasses' => '',
  'dropdownClasses' => '',
])

@php
    $menuAlign = match ($align) {
        'left' => '',
        default => 'dropdown-menu-end',
    };
@endphp

<div class="dropdown {{ $dropdownClasses }}" x-data="{ open: false }">
    <div @click="open = !open" @click.away="open = false">
        {{ $trigger }}
    </div>

    <ul
        x-show="open"
        x-transition
        class="dropdown-menu {{ $menuAlign }} show"
        style="display: none; {{ $width !== 'auto' ? 'width:'.$width.';' : '' }}"
    >
        {{ $content ?? '' }}

        @if (isset($darkModeToggle))
            <li><hr class="dropdown-divider"></li>
            <li class="px-3 py-2">
                {{ $darkModeToggle }}
            </li>
        @endif
    </ul>
</div>
