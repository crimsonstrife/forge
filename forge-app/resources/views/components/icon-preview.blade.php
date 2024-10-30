@props(['selectedIconId' => null, 'icon' => null, 'svgCode' => null, 'svgFilePath' => null])
@php
    // Determine the icon to display based on different contexts.
    $icon = $selectedIconId ? \App\Models\Icon::find($selectedIconId) : $icon;

    // If icon is not yet resolved, check if it's an ID or a related model.
if (!($icon instanceof \App\Models\Icon) && isset($icon)) {
    $icon = \App\Models\Icon::find($icon);
}

// If icon is still not an instance, check for a related record's icon.
    if (!($icon instanceof \App\Models\Icon)) {
        $record = $getRecord();

        // If $record is an icon, use it directly; if not, get the related icon.
        $icon = $record instanceof \App\Models\Icon ? $record : $record?->icon()->first() ?? null;
    }
@endphp

<div class="flex items-center justify-center icon-preview" data-icon-id="{{ $icon->id ?? '' }}">
    @if ($icon)
        @if ($icon->is_builtin)
            <!-- Use BladeUI to render the icon from the built-in set, assuming proper prefix and set registration -->
            <x-dynamic-component :component="$icon->prefix . '-' . $icon->name" />
        @elseif (!$icon->is_builtin && !empty($icon->prefix) && !empty($icon->name))
            @php
                // Check if the component exists
                $component = $icon->prefix . '::' . $icon->name;
                $componentExists = view()->exists($component);
            @endphp
            @if ($componentExists)
                <!-- Use BladeUI to render the icon from a custom set, assuming proper set registration -->
                <x-dynamic-component :component="$icon->prefix . '::' . $icon->name" />
            @else
                <p>No icon component found for {{ $component }}</p>
            @endif
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
        <p>No icon selected/provided</p>
    @endif
</div>
<style>
    .icon-preview svg img {
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

    .icon-preview img {
        stroke: none;
        /* Use no stroke color */
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
