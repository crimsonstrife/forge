<div class="space-y-4">
    <form wire:submit.prevent="add" class="flex gap-2">
        <textarea wire:model.defer="body"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                  rows="3" placeholder="Add a comment..."></textarea>
        <button class="h-10 self-end rounded-lg px-3 bg-primary-600 text-white hover:bg-primary-700">Post</button>
    </form>

    <ul class="divide-y divide-gray-200/60 dark:divide-gray-700/60">
        @forelse($comments as $c)
            <li class="py-3 flex items-start gap-3">
                <img src="{{ $c->user->profile_photo_url }}" class="h-8 w-8 rounded-full" alt="">
                <div>
                    <div class="text-sm font-medium">{{ $c->user->name }}
                        <span class="text-xs text-gray-500">· {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $c->body }}</div>
                </div>
            </li>
        @empty
            <li class="py-6 text-sm text-gray-500">No comments yet.</li>
        @endforelse
    </ul>
</div>
