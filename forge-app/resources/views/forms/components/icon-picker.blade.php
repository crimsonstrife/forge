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
                <div class="flex items-center gap-5">
                    <!-- Current Selected Icon Preview -->
                    <div id="current-icon-preview" wire:key="icon-preview-{{ $getState() }}" class="mr-2">
                        @if ($getState())
                            @php
                                $icon = App\Models\Icon::find($getState());

                                // Get the icon id
                                $iconId = $icon->id ?? null;

                                // set the icon id to the selected icon id
                                $selectedIconId = $iconId;
                            @endphp
                            @if ($selectedIconId)
                                <!-- Use icon-preview component to render the icon -->
                                <x-icon-preview :icon="$selectedIconId" />
                            @else
                                <p>No icon selected</p>
                            @endif
                        @else
                            <p>No icon selected</p>
                        @endif
                    </div>

                    <!-- Button to open the icon picker -->
                    <x-filament::button color="gray" id="icon-picker" type="button" onclick="toggleIconPicker()" class="mr-2">
                        Choose an Icon
                    </x-filament::button>
                </div>

                <!-- Modal with the icon picker -->
                <div id="icon-picker-modal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true" style="display: none;">
                    <div
                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true">
                        </div>

                        <!-- Modal content -->
                        <div id="icon-modal-content"
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full min-w-[600px] min-h-[400px]">
                            <!-- Header Section with Modal Title -->
                            <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                                    Icon Picker
                                </h3>
                            </div>
                            <!-- Header Section with Filters and Search Bar -->
                            <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row">
                                    <x-filament::input.wrapper>
                                    <x-filament::input.select id="icon-type-filter" onchange="filterIcons()">
                                        @foreach ($getTypes() as $type)
                                            <option value="{{ $type }}" @if ($type === 'fontawesome') selected @endif>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                    <x-filament::input.wrapper>
                                    <x-filament::input.select id="icon-style-filter" onchange="filterIcons()">
                                        <option value="">All Styles</option>
                                        @foreach ($getStyles() as $style)
                                            <option value="{{ $style }}" @if ($style === 'regular') selected @endif>{{ ucfirst($style) }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                    <x-filament::input.wrapper prefix-icon="far-magnifying-glass">
                                    <x-filament::input type="text" id="icon-search-input" placeholder="Search icons..." class="w-full sm:w-1/3" oninput="debounce(filterIcons, 300)" />
                                    </x-filament::input.wrapper>
                                </div>
                            </div>
                            <!-- Grid of icons with responsive columns -->
                            <div id="icon-picker-grid"
                            class="grid grid-cols-1 gap-4 overflow-y-auto sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 max-h-96">
                            <!-- Icons will load here dynamically -->
                            <!-- Loading Indicator -->
                                <div id="loading-overlay" class="absolute inset-0 flex items-center justify-center hidden bg-gray-200 bg-opacity-50 dark:bg-gray-700">
                                    <x-filament::loading-indicator class="w-10 h-10 text-blue-500" />
                                </div>
                        </div>
                            <!-- Action buttons -->
                            <div class="flex items-center justify-start gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6">
                                <!-- Confirm button -->
                                <x-filament::button primary type="button" onclick="confirmSelection('{{ $getState() }}')">
                                    Confirm Selection
                                </x-filament::button>
                                <!-- Load More button -->
                                <x-filament::button type="button" color="gray" onclick="loadMoreIcons()" class="ml-3">
                                    Load More
                                </x-filament::button>
                                <!-- Close button -->
                                <x-filament::button secondary color="gray" type="button" onclick="toggleIconPicker()" class="ml-auto">
                                    Close
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadMoreIcons(true); // Initial load on page load
    });

    let currentPage = 1;
    let isFetching = false;
    let debounceTimeout;
    let hasLoadedInitially = false;
    let selectedIconId = '{{ $getState() }}';
    const loadingOverlay = document.getElementById('loading-overlay');

    function loadMoreIcons(reset = false) {
        if (isFetching) return;
        isFetching = true;

        const iconGrid = document.getElementById('icon-picker-grid');
        if (reset) {
            currentPage = 1;
            iconGrid.innerHTML = ''; // Clear existing icons if resetting
        }
        const typeFilter = document.getElementById('icon-type-filter').value;
        const styleFilter = document.getElementById('icon-style-filter').value;
        const searchQuery = document.getElementById('icon-search-input').value ?? '';

        // Show loading overlay
        loadingOverlay.classList.remove('hidden');

        fetch(`/api/icons?type=${typeFilter}&style=${styleFilter}&search=${searchQuery}&page=${currentPage}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok " + response.statusText);
                }
                return response.text();
            })
            .then(text => {
                console.log("Fetched icons JSON:", text); // Log the raw JSON response
                const data = JSON.parse(text);
                // Hide loading overlay
                loadingOverlay.classList.add('hidden');
                data.icons.forEach(iconHtml => {
                    if (iconHtml && iconHtml !== 'undefined') {
                        const iconElement = document.createElement('div');
                        iconElement.innerHTML = iconHtml;
                        iconElement.classList.add('icon-picker-button');
                        iconElement.onclick = () => {
                            const iconId = iconElement.querySelector('[data-icon-id]').dataset.iconId;
                            if (iconId) selectIcon(iconId);
                        };
                        iconGrid.appendChild(iconElement);
                    } else {
                        console.warn('Skipping undefined icon element');
                    }
                });

                if (data.pagination && currentPage < data.pagination.lastPage) {
                    currentPage++;
                }
                isFetching = false;
            })
            .catch(error => {
                console.error('Error loading icons:', error);
                // Ensure loading indicator is removed on error
                loadingOverlay.classList.add('hidden');
                isFetching = false;
            });
    }

    // Debounce function to avoid multiple API calls
    function debounce(fn, delay) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(fn, delay);
    }

    function toggleIconPicker() {
        const modal = document.getElementById('icon-picker-modal');
        const isShown = modal.style.display === 'none';
        modal.style.display = isShown ? 'block' : 'none';

        // If the modal is shown for the first time, load the icons
        if (isShown && !hasLoadedInitially) {
            loadMoreIcons(true);
            hasLoadedInitially = true;
        }

        if (isShown && hasLoadedInitially) {
            debounce(() => loadMoreIcons(true), 300);
        }

        // Reset the debounce timeout when the modal is closed
        if (!isShown) {
            debounce(() => loadMoreIcons(true), 300);
        }
    }

    function selectIcon(iconId) {
        @this.set('{{ $getStatePath() }}', iconId); // Save the selected icon's ID as before
        selectedIconId = iconId; // Set the selected icon ID for the preview
        updateCurrentIconPreview(iconId);
    }

    function confirmSelection() {
        // Get the selected icon ID from the state if it's set
        const iconId = '{{ $getState() }}';
        // Emit the selected icon ID to the parent component
        @this.set('{{ $getStatePath() }}', iconId);
        // Close the modal
        toggleIconPicker();
    }

    function filterIcons() {
        loadMoreIcons(true); // Reload with filters
        debounce(() => loadMoreIcons(true), 300);
    }

    function updateCurrentIconPreview(iconId) {
        if (!iconId) {
            document.getElementById('current-icon-preview').innerHTML = '<p>No icon selected</p>';
            return;
        }

        @this.set('selectedIconId', iconId); // Emit Livewire update for `selectedIconId`
    }

    // Lazy load more icons when the user scrolls to the bottom
    document.getElementById('icon-picker-grid').addEventListener('scroll', function() {
        if (this.scrollTop + this.clientHeight >= this.scrollHeight) {
            loadMoreIcons();
        }
    });
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
    #icon-picker-modal svg,
    #icon-picker-modal img {
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

    #current-icon-preview svg,
    #current-icon-preview img {
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
</style>
