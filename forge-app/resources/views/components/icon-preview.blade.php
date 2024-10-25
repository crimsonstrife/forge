<div class="icon-preview flex items-center justify-center">
    @php
        $icon = $getRecord();
    @endphp

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
</style>
