@php
    /** @var \App\Models\Comment $c */
    /** @var int $level */
    $indentClass = 'ms-' . min(5, $level + 1); // cap indent
    $children = $c->getRelation('children_eager') ?? collect();
@endphp

<li class="list-group-item">
    <div class="d-flex gap-2">
        <x-avatar :src="$c->user->profile_photo_url" :name="$c->user->name" preset="sm" />
        <div class="flex-grow-1">
            <div class="small fw-semibold">
                {{ $c->user->name }}
                <span class="text-body-secondary fw-normal">· {{ $c->created_at->diffForHumans() }}</span>
            </div>

            <div class="small">{{ $c->body }}</div>

            <div class="d-flex gap-2 align-items-center mt-2">
                <button type="button"
                        class="btn btn-sm btn-link p-0"
                        wire:click="startReply({{ $c->id }})">
                    {{ __('Reply') }}
                </button>
            </div>

            {{-- Inline reply box --}}
            @if ($replyFor === $c->id)
                <form wire:submit.prevent="postReply({{ $c->id }})" class="d-flex gap-2 mt-2 {{ $indentClass }}">
                    <wa-textarea class="form-control"
                                 wire:model.defer="replyBodies.{{ $c->id }}"
                                 rows="2"
                                 placeholder="{{ __('Write a reply...') }}"></wa-textarea>
                    <div class="d-flex flex-column gap-2">
                        <button class="btn btn-primary btn-sm">{{ __('Reply') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="cancelReply">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>
            @endif

            {{-- Children --}}
            @if ($children->isNotEmpty())
                <div class="border-start ps-3 mt-3 {{ $indentClass }}">
                    <ul class="list-unstyled d-grid gap-3">
                        @foreach ($children as $child)
                            @include('livewire.issues.partials.comment', ['c' => $child, 'level' => $level + 1])
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</li>
