<div>
    <div class="flex items-center">
        <input
            type="text"
            wire:model.defer="newTag"
            placeholder="Add a tag"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
        />
        <button
            type="button"
            wire:click="addTag"
            class="px-4 py-2 ml-2 text-white bg-blue-500 rounded-md"
        >
            Add
        </button>
    </div>

    <div class="mt-2 space-x-2">
        @foreach ($availableTags as $tag)
            <span
                class="inline-flex items-center px-2 py-1 text-sm text-white bg-gray-700 rounded-full"
            >
                {{ $tag->name }}
                <button
                    type="button"
                    wire:click="removeTag({{ $tag->id }})"
                    class="ml-1 text-red-500 hover:text-red-700"
                >
                    &times;
                </button>
            </span>
        @endforeach
    </div>
</div>
