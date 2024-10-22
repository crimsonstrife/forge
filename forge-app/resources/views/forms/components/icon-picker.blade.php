<div data-field-wrapper="" class="fi-fo-field-wrp">
    <div class="grid gap-y-2">
        <div>
            <!-- Button to open the icon picker -->
            <button type="button" onclick="toggleIconPicker()"
                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg  fi-btn-color-gray fi-color-gray fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20 [input:checked+&]:bg-gray-400 [input:checked+&]:text-white [input:checked+&]:ring-0 [input:checked+&]:hover:bg-gray-300 dark:[input:checked+&]:bg-gray-600 dark:[input:checked+&]:hover:bg-gray-500 fi-ac-action fi-ac-btn-action">
                Select Icon
            </button>

            <!-- Modal with the icon picker -->
            <div id="icon-picker-modal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <!-- Modal content -->
                    <div id="icon-modal-content"
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full min-w-[600px] min-h-[400px]">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <!-- Grid of icons with responsive columns -->
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 max-h-96 overflow-y-auto">
                                @foreach ($getIcons() as $icon)
                                    <div onclick="selectIcon('{{ $icon->id }}')"
                                        class="cursor-pointer border p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 icon-picker-button">
                                        @if (!empty($icon->svg_file_path) && file_exists(public_path($icon->svg_file_path)))
                                            <!-- Load the SVG file as <svg> -->
                                            {!! file_get_contents(public_path($icon->svg_file_path)) !!}
                                        @elseif (!empty($icon->svg_code))
                                            <!-- Fall back to loading the raw SVG code from the database -->
                                            {!! $icon->svg_code !!}
                                        @else
                                            <!-- Handle case when no file or code is available -->
                                            <p>No icon available</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button onclick="toggleIconPicker()" class="px-4 py-2 bg-blue-600 text-white rounded">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleIconPicker() {
        const modal = document.getElementById('icon-picker-modal');
        modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
    }

    function selectIcon(iconId) {
        @this.set('{{ $getStatePath() }}', iconId);
        toggleIconPicker(); // Close the modal after selecting
    }
</script>
<style>
    /* Ensure the modal is centered */
    #icon-picker-modal {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Ensure the modal is hidden by default */
    #icon-picker-modal {
        display: none;
    }

    /* Keep the modal elements displaying correctly */
    #icon-picker-modal #icon-modal-content {
        display: inline-table;
    }

    /* Ensure the modal is a decent width, but not too wide */
    #icon-picker-modal #icon-modal-content {
        width: 40vw;
        /* Set the width to 40% of the viewport */
        max-width: 600px;
        /* Set a maximum width */
    }

    /* Ensure the modal is a decent height, but not too tall */
    #icon-picker-modal #icon-modal-content {
        height: 60vh;
        /* Set the height to 60% of the viewport */
        max-height: 400px;
        /* Set a maximum height */
    }

    /* Keep the modal from being too high and clipping into the header */
    #icon-picker-modal #icon-modal-content {
        top: 20vh;
        /* Set the top to 20% of the viewport */
        position: sticky;
        /* Keep the modal in place */
    }

    /* Set the SVG size and color */
    #icon-picker-modal svg {
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

    /* Hover state to change color */
    #icon-picker-modal svg:hover {
        fill: #3b82f6;
        /* Example hover color */
    }

    /* Make sure the grid is responsive */
    #icon-picker-modal .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
        /* Ensure responsive columns */
        gap: 1rem;
    }

    /* Modal background and grid behavior */
    #icon-picker-modal .grid {
        max-height: 400px;
        /* Set max height for the grid */
        overflow-y: auto;
        /* Enable vertical scrolling for icons if needed */
    }

    /* Make sure the buttons are responsive */
    #icon-picker-modal .icon-picker-button {
        display: flex;
        justify-content: center;
        align-items: center;
        border-style: none;
        /* Remove any border */
    }

    /* Handle the ::before and ::after pseudo-elements of the buttons */
    #icon-picker-modal .icon-picker-button::before,
    #icon-picker-modal .icon-picker-button::after {
        border-style: none;
        /* Remove any border */
    }

    /* Handle the hover state of the buttons */
    #icon-picker-modal .icon-picker-button:hover {
        /* Only slightly brighten the background */
        background-color: rgba(0, 0, 0, 0.05);
    }
</style>
