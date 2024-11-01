@props(['selectedIconId' => null, 'icon' => null, 'svgCode' => null, 'svgFilePath' => null])
@php
    $contextOptions = [];

    if (app()->environment('local')) {
        $contextOptions['ssl'] = [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ];
    }
    $context = stream_context_create($contextOptions);
@endphp
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
                <x-dynamic-component :component="$icon->prefix . '-' . $icon->name" />
            @elseif (!empty($icon->svg_code))
                <!-- Render the custom SVG code directly -->
                @php
                    // Get the SVG code from the database
                    $svgCode = $icon->svg_code;

                    // Add the icon set class to the SVG code, if a class parameter is already set, then append the icon set class
                    $iconSetClasses = $icon->class;
                    // Does the SVG code already have a class attribute?
                    if (strpos($svgCode, 'class=') !== false) {
                        // If so, append the icon set class to the existing class attribute
                        $svgCode = preg_replace('/class="([^"]*)"/', 'class="$1 ' . $iconSetClasses . '"', $svgCode);
                    } else {
                        // If not, add the icon set class to the SVG code
                        $svgCode = str_replace('<svg', '<svg class="' . $iconSetClasses . '"', $svgCode);
                    }

                    // Regex to remove preset fill styles
                    $svgCode = preg_replace('/fill="[^"]*"/', '', $svgCode);
                @endphp
                {!! $icon->svg_code !!}
            @elseif (!empty($icon->svg_file_path))
                <!-- If a custom file is used, render the SVG from the file -->
                @php
                    // Get the SVG file path and ensure it exists
                    $svgFilePath = Storage::url($icon->svg_file_path);
                    // Get the SVG code from the file
                    $svgCode = Storage::disk('public')->exists($icon->svg_file_path) ? file_get_contents(asset($svgFilePath), false, $context) : '<p>No icon available</p>';

                    // Add the icon set class to the SVG code
                    $iconSetClasses = $icon->class;
                    // Does the SVG code already have a class attribute?
                    if (strpos($svgCode, 'class=') !== false) {
                        // If so, append the icon set class to the existing class attribute
                        $svgCode = preg_replace('/class="([^"]*)"/', 'class="$1 ' . $iconSetClasses . '"', $svgCode);
                    } else {
                        // If not, add the icon set class to the SVG code
                        $svgCode = str_replace('<svg', '<svg class="' . $iconSetClasses . '"', $svgCode);
                    }

                    // Regex to remove preset fill styles
                    $svgCode = preg_replace('/fill="[^"]*"/', '', $svgCode);
                @endphp
                {!! $svgCode !!}
            @else
                <p>No icon available</p>
            @endif
        @else
            <p>No icon selected/provided</p>
        @endif
    @else
        <p>No icon selected/provided</p>
    @endif
</div>
<style>
    .icon-preview svg,
    .icon-preview img {
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
