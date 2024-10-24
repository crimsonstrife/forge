<div class="icon-preview flex items-center justify-center">
    @if (!empty($getRecord()->getSvg($getRecord()->id)))
        @if ($getRecord()->isFile($getRecord()->id))
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
            {!! file_get_contents($getRecord()->getSvg($getRecord()->id), false, $context) !!}
        @else
            {!! $getRecord()->getSvg($getRecord()->id) !!}
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
