<div class="rounded-lg border bg-white dark:bg-gray-800">
    <div class="flex items-center justify-between p-4">
        <div class="space-y-1">
            <h3 class="text-base font-semibold">Time Tracked</h3>
            <div class="text-2xl font-bold tabular-nums">
                {{ gmdate('H:i:s', max(0, $totalSeconds)) }}
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Scope:</span>
            <button
                type="button"
                class="px-3 py-1.5 rounded border text-sm"
                wire:click="toggleScope"
            >
                {{ $showAll ? 'All Users' : 'Only Me' }}
            </button>
        </div>
    </div>

    <div class="border-t">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/30">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Started</th>
                    <th class="px-4 py-2 text-left font-medium">Ended</th>
                    <th class="px-4 py-2 text-left font-medium">Duration</th>
                    <th class="px-4 py-2 text-left font-medium">Source</th>
                    @if ($showAll)
                        <th class="px-4 py-2 text-left font-medium">User</th>
                    @endif
                    <th class="px-4 py-2 text-left font-medium">Notes</th>
                    <th class="px-4 py-2"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($entries as $row)
                    <tr @class(['bg-yellow-50/40 dark:bg-yellow-900/10' => $row['is_running']])>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $row['started_at'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            {{ $row['ended_at'] ?? '—' }}
                        </td>
                        <td class="px-4 py-2 font-medium tabular-nums">
                            {{ gmdate('H:i:s', max(0, $row['duration_seconds'])) }}
                            @if($row['is_running'])
                                <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs border">
                                        Running
                                    </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ ucfirst($row['source']) }}</td>
                        @if ($showAll)
                            <td class="px-4 py-2">{{ $row['user'] }}</td>
                        @endif
                        <td class="px-4 py-2 max-w-[28rem]">
                            @if ($editingId === $row['id'])
                                <textarea
                                    wire:model.defer="editingNotes"
                                    rows="2"
                                    class="w-full rounded border p-2 bg-white dark:bg-gray-900"
                                ></textarea>
                                <div class="mt-1 flex gap-2">
                                    <button type="button" class="px-2.5 py-1 rounded bg-indigo-600 text-white text-xs hover:bg-indigo-700" wire:click="saveNote">Save</button>
                                    <button type="button" class="px-2.5 py-1 rounded border text-xs" wire:click="cancelEdit">Cancel</button>
                                </div>
                            @else
                                <div class="truncate" title="{{ $row['notes'] ?? '' }}">{{ $row['notes'] ?? '—' }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($row['is_running'])
                                <button type="button" class="px-2.5 py-1 rounded bg-rose-600 text-white text-xs hover:bg-rose-700" wire:click="stopEntry('{{ $row['id'] }}')">Stop</button>
                            @else
                                <button type="button" class="px-2.5 py-1 rounded border text-xs" wire:click="edit('{{ $row['id'] }}')">Edit</button>
                            @endif
                        </td>

                        <td class="px-4 py-2 text-right">
                            @if ($row['is_running'])
                                <button
                                    type="button"
                                    class="px-2.5 py-1 rounded bg-rose-600 text-white text-xs hover:bg-rose-700"
                                    wire:click="stopEntry('{{ $row['id'] }}')"
                                >Stop</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-gray-500 dark:text-gray-400" colspan="{{ $showAll ? 7 : 6 }}">
                            No time tracked yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
