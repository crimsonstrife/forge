<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $board->name }}</h1>
            <div class="text-muted small">{{ $board->product?->name }}</div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="list-group">
                @foreach($posts as $post)
                    <button type="button" wire:click="selectPost('{{ $post->id }}')" class="list-group-item list-group-item-action {{ $selectedPost?->is($post) ? 'active' : '' }}">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>{{ $post->key }}</strong>
                            <span>{{ $post->net_score }}</span>
                        </div>
                        <div>{{ $post->title }}</div>
                        <small>{{ $post->status?->name }} · {{ $post->comment_count }} comments</small>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="col-md-8">
            @if($selectedPost)
                <article class="mb-4">
                    <div class="d-flex justify-content-between">
                        <h2 class="h5">{{ $selectedPost->title }}</h2>
                        <span class="badge text-bg-secondary">{{ $selectedPost->status?->name }}</span>
                    </div>
                    <div class="text-muted small mb-3">{{ $selectedPost->identity?->display_name }} · {{ $selectedPost->created_at?->diffForHumans() }}</div>
                    <div class="prose">{!! $selectedPost->body_html !!}</div>
                </article>

                <div class="mb-4">
                    @foreach($selectedPost->comments as $comment)
                        <div class="border-top py-3">
                            <div class="small text-muted">
                                {{ $comment->is_staff_reply ? ($comment->staffUser?->name ?? 'Staff').' (Staff)' : $comment->identity?->display_name }}
                                · {{ $comment->created_at?->diffForHumans() }}
                            </div>
                            <div>{!! $comment->body_html !!}</div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit="submitReply" class="vstack gap-2">
                    <textarea wire:model="replyBody" class="form-control" rows="5" placeholder="Reply as staff"></textarea>
                    @error('replyBody') <div class="text-danger small">{{ $message }}</div> @enderror
                    <div>
                        <button type="submit" class="btn btn-primary">Reply</button>
                    </div>
                </form>
            @else
                <div class="text-muted">No feedback posts yet.</div>
            @endif
        </div>
    </div>
</div>
