<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            {{ __('Create Report for ') . $dashboard->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <form action="{{ route('reports.store') }}" method="POST">
                    @csrf
                    <label for="name">Report Name:</label>
                    <input type="text" name="name" id="name" required>

                    <label for="template">Select Template:</label>
                    <select name="template_id" id="template">
                        <option value="">None</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>

                    <label for="filters">Filters (JSON):</label>
                    <textarea name="filters" id="filters"></textarea>

                    <button type="submit">Create Report</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('template_id').addEventListener('change', function() {
            const selectedTemplate = this.options[this.selectedIndex];
            const settings = selectedTemplate.dataset.settings;

            if (settings) {
                // Populate fields dynamically using JSON.parse(settings)
            }
        });
    </script>
</x-app-layout>
