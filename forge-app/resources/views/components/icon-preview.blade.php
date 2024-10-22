<div class="icon-preview flex items-center justify-center">
    @if (!empty($getRecord()->svg_file_path) && file_exists(public_path($getRecord()->svg_file_path)))
        <!-- Render the SVG file -->
        {!! file_get_contents(public_path($getRecord()->svg_file_path)) !!}
    @elseif (!empty($getRecord()->svg_code))
        <!-- Render the SVG code from the database -->
        {!! $getRecord()->svg_code !!}
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
