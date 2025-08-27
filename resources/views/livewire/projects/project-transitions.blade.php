<div class="rounded-xl border border-gray-200/60 dark:border-gray-700/60 bg-white dark:bg-gray-800 p-4">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr>
                <th class="text-left p-2">From \ To</th>
                @foreach($statuses as $to)
                    <th class="p-2 text-left">{{ $to->name }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($statuses as $from)
                <tr class="border-t border-gray-200/60 dark:border-gray-700/60">
                    <th class="p-2 text-left font-medium">{{ $from->name }}</th>
                    @foreach($statuses as $to)
                        <td class="p-2">
                            @if($from->id === $to->id)
                                <span class="text-xs text-gray-400">—</span>
                            @else
                                @php $k = $from->id . ':' . $to->id; @endphp
                                <input type="checkbox" wire:model.defer="matrix.{{ $k }}">
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-end gap-3">
        <a href="{{ route('projects.show', ['project'=>$project]) }}" class="px-3 py-2 rounded-lg border">Back</a>
        <x-button wire:click="save">Save</x-button>
    </div>
</div>
