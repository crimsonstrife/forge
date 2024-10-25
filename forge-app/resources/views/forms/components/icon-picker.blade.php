<div data-field-wrapper="" class="fi-fo-field-wrp">
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
    <div class="grid gap-y-2">
        <div class="flex items-center justify-between gap-x-3">
            <label for="icon-picker" class="inline-flex items-center fi-fo-field-wrp-label gap-x-3">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Icon</span>
                <span class="fi-fo-field-wrp-label-hint">Select an icon from the available options.</span>
            </label>
        </div>
        <div class="grid auto-cols-fr gap-y-2">
            <div>
                <div class="flex items-center">
                    <!-- Current Selected Icon Preview -->
                    <div id="current-icon-preview" class="mr-2">
                        @if ($getState())
                            @php
                                $icon = App\Models\Icon::find($getState());
                            @endphp
                            @if ($icon)
                                @if (!empty($icon->getSvg($icon->id)))
                                    <!-- If the icon is a file path, render the SVG file -->
                                    @if (!empty($icon->getSvg($icon->id)) && $icon->isFile($icon->id))
                                        <img src="{{ $icon->getSvg($icon->id) }}" alt="icon-{{ $icon->name }}" />
                                    @else
                                        <!-- If the icon is a code, render the SVG code -->
                                        {!! $icon->getSvg($icon->id) !!}
                                    @endif
                                @else
                                    <p>No icon selected</p>
                                @endif
                            @else
                                <p>No icon selected</p>
                            @endif
                        @else
                            <p>No icon selected</p>
                        @endif
                    </div>

                    <!-- Button to open the icon picker -->
                    <button id="icon-picker" type="button" onclick="toggleIconPicker()"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg  fi-btn-color-gray fi-color-gray fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20 [input:checked+&]:bg-gray-400 [input:checked+&]:text-white [input:checked+&]:ring-0 [input:checked+&]:hover:bg-gray-300 dark:[input:checked+&]:bg-gray-600 dark:[input:checked+&]:hover:bg-gray-500 fi-ac-action fi-ac-btn-action">
                        Select Icon
                    </button>
                </div>

                <!-- Modal with the icon picker -->
                <div id="icon-picker-modal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                        <!-- Modal content -->
                        <div id="icon-modal-content"
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full min-w-[600px] min-h-[400px]">
                            <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                                <!-- Filters for Icon Type and Style -->
                                <div class="flex justify-between mb-4">
                                    <select id="icon-type-filter" class="px-2 py-1 border rounded" onchange="filterIcons()">
                                        @foreach ($getTypes() as $type)
                                            <!-- If the current type is fontawesome, set it selected -->
                                            <option value="{{ $type }}"
                                                @if ($type === 'fontawesome') selected @endif>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                    <select id="icon-style-filter" class="px-2 py-1 border rounded" onchange="filterIcons()">
                                        <option value="" selected>All Styles</option>
                                        @foreach ($getStyles() as $style)
                                            <option value="{{ $style }}">{{ ucfirst($style) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Grid of icons with responsive columns -->
                                <div id="icon-picker-grid"
                                    class="grid grid-cols-1 gap-4 overflow-y-auto sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 max-h-96">
                                    @foreach ($getIcons() as $icon)
                                        <div onclick="selectIcon('{{ $icon->id }}')" data-type="{{ $icon->type }}"
                                            data-style="{{ $icon->style }}"
                                            class="p-2 border rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 icon-picker-button">
                                            @if (!empty($icon->getSvg($icon->id)))
                                                <!-- Render the SVG file -->
                                                @if ($isFile($icon->id))
                                                    <img src="{{ $icon->getSvg($icon->id) }}" alt="icon-{{ $icon->name }}" />
                                                @else
                                                    {!! $icon->getSvg($icon->id) !!}
                                                @endif
                                            @else
                                                <!-- Handle case when no file or code is available -->
                                                <p>No icon available</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button onclick="toggleIconPicker()" class="px-4 py-2 text-white bg-blue-600 rounded">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let currentPage = 1;

    function loadMoreIcons() {
        fetch(`/api/icons?page=${currentPage + 1}`)
            .then(response => response.json())
            .then(data => {
                const iconGrid = document.getElementById('icon-grid');
                data.icons.forEach(icon => {
                    const iconElement = document.createElement('div');
                    iconElement.onclick = () => selectIcon(icon.id);
                    iconElement.setAttribute('data-type', icon.type);
                    iconElement.setAttribute('data-style', icon.style);
                    iconElement.classList.add('p-2', 'border', 'rounded-lg', 'cursor-pointer', 'hover:bg-gray-100', 'dark:hover:bg-gray-700', 'icon-picker-button');
                    if (icon.svg) {
                        if (icon.is_file) {
                            const img = document.createElement('img');
                            img.src = icon.svg;
                            img.alt = `icon-${icon.name}`;
                            iconElement.appendChild(img);
                        } else {
                            iconElement.innerHTML = icon.svg;
                        }
                    } else {
                        iconElement.innerHTML = '<p>No icon available</p>';
                    }
                    iconGrid.appendChild(iconElement);
                });
                currentPage++;
            });
    }

    function toggleIconPicker() {
        const modal = document.getElementById('icon-picker-modal');
        const isShown = modal.style.display === 'none';
        modal.style.display = modal.style.display === 'none' ? 'block' : 'none';

        // Apply filters when the modal is opened
        if (isShown) {
            filterIcons();
        }

        // Reset the current page when the modal is closed
        if (currentPage === 1) {
            loadMoreIcons();
        }
    }

    function selectIcon(iconId) {
        @this.set('{{ $getStatePath() }}', iconId);
        updateCurrentIconPreview(iconId); // Update the preview when a new icon is selected
        toggleIconPicker(); // Close the modal after selecting
    }

    function filterIcons() {
        const typeFilter = document.getElementById('icon-type-filter').value;
        const styleFilter = document.getElementById('icon-style-filter').value;
        const icons = document.querySelectorAll('#icon-picker-grid .icon-picker-button');

        icons.forEach(icon => {
            const type = icon.getAttribute('data-type');
            const style = icon.getAttribute('data-style');

            const matchesType = !typeFilter || type === typeFilter;
            const matchesStyle = !styleFilter || style === styleFilter;

            if (matchesType && matchesStyle) {
                icon.style.display = 'block';
            } else {
                icon.style.display = 'none';
            }
        });
    }

    function updateCurrentIconPreview(iconId) {
        // Reset the preview if no icon is selected
        if (!iconId) {
            document.getElementById('current-icon-preview').innerHTML = '<p>No icon selected</p>';
            return;
        }

        // Fetch the selected icon's SVG and update the preview
        fetch(`/icons/${iconId}/svg`)
            .then(response => response.json())
            .then(icon => {
                const preview = document.getElementById('current-icon-preview');
                if (icon.svg) {
                    preview.innerHTML = icon.svg;
                } else {
                    preview.innerHTML = '<p>No icon selected</p>';
                }
            })
            .catch(() => {
                document.getElementById('current-icon-preview').innerHTML = '<p>No icon selected</p>';
            });
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
    #icon-picker-modal svg, #icon-picker-modal img {
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

    #current-icon-preview svg, #current-icon-preview img {
        min-width: 32px;
        /* Ensure a minimum width */
        min-height: 32px;
        /* Ensure a minimum height */
        max-width: 64px;
        /* Ensure the SVG scales */
        max-height: 64px;
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

    /* Set the SVG path fill color */
    #icon-picker-modal svg path {
        fill: currentColor;
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
