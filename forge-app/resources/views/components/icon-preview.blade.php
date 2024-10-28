@props(['selectedIconId' => null, 'icon' => null])
    @php
        $icon = $selectedIconId ? \App\Models\Icon::find($selectedIconId) : $icon;

        // if the passed icon is not an instance of the Icon model, we will try to find it by the ID
        if (!$icon instanceof \App\Models\Icon && $icon) {
            $icon = \App\Models\Icon::find($icon);
        }
    @endphp

<div class="flex items-center justify-center icon-preview" data-icon-id="{{ $icon->id ?? '' }}">
    @if ($icon)
        @if ($icon->is_builtin)
            <!-- Use BladeUI to render the icon from the built-in set, assuming proper prefix and set registration -->
            <x-dynamic-component :component="$icon->prefix . '-' . $icon->name" />
        @elseif (!empty($icon->svg_code))
            <!-- Render the custom SVG code directly -->
            {!! $icon->svg_code !!}
        @elseif (!empty($icon->svg_file_path))
            <!-- If a custom file is used, render the SVG from the file path -->
            <img src="{{ Storage::url($icon->svg_file_path) }}" alt="icon-{{ $icon->name }}" />
        @else
            <p>No icon available</p>
        @endif
    @else
        <p>No icon available</p>
    @endif
</div>
<style>
    .icon-preview svg {
        min-width: 16px;
        /* Ensure a minimum width */
        min-height: 16px;
        /* Ensure a minimum height */
        max-width: 32px;
        /* Ensure the SVG scales */
        max-height: 32px;
        /* Ensure the SVG scales */
        width: 100%;
        /* Ensure the SVG scales */
        height: 100%;
        /* Ensure the SVG scales */
        background-color: transparent;
        /* Ensure no background color */
        stroke: currentColor;
        /* Use the current text color */
    }

    .icon-preview svg path {
        fill: currentColor;
        /* Use the current text color */
    }

    /* Exempt the Heroicons set outline style from the fill color */
    .icon-preview svg.hero-icon-set.heroo-icon path {
        fill: none;
        /* Use no fill color */
    }

    /* Exempt the Heroicons set solid style from the stroke width */
    .icon-preview svg.hero-icon-set.heros-icon path {
        stroke-width: 0px;
        /* Use no stroke width */
    }

    /* Exempt the Octicon set solid style from the stroke width */
    .icon-preview svg.octi-icon-set.octis-icon path {
        stroke-width: 0px;
        /* Use no stroke width */
    }
</style>
