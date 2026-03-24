@php
    /** @var \App\Models\Comment $c */
    /** @var int $level */
    $MAX_INDENT_LEVEL = 5;
    $indentClass = 'ms-' . min($MAX_INDENT_LEVEL, $level + 1); // cap indent
    $children = $c->getRelation('children_eager') ?? collect();
@endphp

<li class="list-group-item" wire:key="comment-{{ $c->id }}" id="comment-{{ $c->id }}">
    <div class="d-flex gap-2">
        <x-avatar :src="$c->user->profile_photo_url" :name="$c->user->name" preset="sm" />
        <div class="flex-grow-1">
            <div class="small fw-semibold">
                {{ $c->user->name }}
                <span class="text-body-secondary fw-normal">· {{ $c->created_at->diffForHumans() }}</span>
            </div>

            <div class="small issue-content">{!! $c->rendered_body !!}</div>

            <button type="button"
                    class="btn btn-sm btn-link p-0"
                    wire:click="startReply(@js($c->id))">
                {{ __('Reply') }}
            </button>

            {{-- Inline reply box --}}
            @if ($replyFor === $c->id)
                <form wire:submit.prevent="postReply(@js($c->id))" class="d-grid gap-2 mt-2 {{ $indentClass }}">
                    <x-editor.tiny
                        :id="'issue-reply-'.$c->id.'-'.($replyEditorNonces[$c->id] ?? 0)"
                        :name="'reply-'.$c->id"
                        :wireModel="'replyBodies.'.$c->id"
                        :value="$replyBodies[$c->id] ?? ''"
                        :height="150"
                        toolbar="undo redo | bold italic link | bullist numlist | mentionUser mentionIssue"
                        plugins="link lists"
                    />
                    @error("replyBodies.$c->id") <div class="form-text text-danger">{{ $message }}</div> @enderror
                    <div class="d-flex justify-content-end gap-2">
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
