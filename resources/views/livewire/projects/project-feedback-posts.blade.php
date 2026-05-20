<div class="d-flex flex-column gap-4">
    @if($boards->isEmpty())
        <div class="rounded border bg-body-tertiary p-4">
            <h3 class="h6 mb-1">{{ __('No feedback boards connected') }}</h3>
            <p class="small text-body-secondary mb-0">
                {{ __('Connect a service product with feedback boards to this project to see customer posts here.') }}
            </p>
        </div>
    @else
        <div class="rounded border bg-body-tertiary p-3">
            <div class="d-flex flex-wrap align-items-end gap-2">
                <div style="min-width: 16rem;">
                    <label class="form-label small mb-1">{{ __('Board') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="boardId">
                        @foreach($boards as $option)
                            <option value="{{ $option->id }}">
                                {{ $option->name }} @if($option->product?->name) · {{ $option->product->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 12rem;">
                    <label class="form-label small mb-1">{{ __('Status') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="status">
                        <option value="">{{ __('All statuses') }}</option>
                        @foreach($board?->statuses ?? [] as $statusOption)
                            <option value="{{ $statusOption->slug }}">{{ $statusOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 12rem;">
                    <label class="form-label small mb-1">{{ __('Category') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="category">
                        <option value="">{{ __('All categories') }}</option>
                        @foreach($board?->categories ?? [] as $categoryOption)
                            <option value="{{ $categoryOption->slug }}">{{ $categoryOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 12rem;">
                    <label class="form-label small mb-1">{{ __('Sort') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="sort">
                        <option value="top">{{ __('Top') }}</option>
                        <option value="new">{{ __('New') }}</option>
                        <option value="trending">{{ __('Trending') }}</option>
                    </select>
                </div>

                <div class="flex-grow-1" style="min-width: 18rem;">
                    <label class="form-label small mb-1">{{ __('Search') }}</label>
                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search"
                           placeholder="{{ __('Search key or title') }}">
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5 col-xl-4">
                <div class="list-group">
                    @forelse($posts as $post)
                        <button type="button"
                                wire:click="selectPost('{{ $post->id }}')"
                                class="list-group-item list-group-item-action {{ $selectedPost?->is($post) ? 'active' : '' }}">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="min-w-0">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="fw-semibold">{{ $post->key }}</span>
                                        @if($post->is_pinned)
                                            <span class="badge text-bg-primary">{{ __('Pinned') }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1">{{ $post->title }}</div>
                                    <div class="small mt-1 {{ $selectedPost?->is($post) ? 'text-white-50' : 'text-body-secondary' }}">
                                        {{ $post->identity?->display_name ?? __('Unknown') }}
                                        · {{ $post->status?->name ?? __('No status') }}
                                        · {{ $post->comments_count }} {{ __('comments') }}
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-semibold">{{ $post->net_score }}</div>
                                    <div class="small {{ $selectedPost?->is($post) ? 'text-white-50' : 'text-body-secondary' }}">{{ __('score') }}</div>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="rounded border bg-body-tertiary p-4 small text-body-secondary">
                            {{ __('No feedback posts match these filters.') }}
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $posts->links() }}
                </div>
            </div>

            <div class="col-lg-7 col-xl-8">
                @if($selectedPost)
                    <article class="rounded border bg-body p-4">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="badge text-bg-secondary">{{ $selectedPost->key }}</span>
                                    @if($selectedPost->status)
                                        <span class="badge" style="background-color: {{ $selectedPost->status->color }}20; color: {{ $selectedPost->status->color }};">
                                            {{ $selectedPost->status->name }}
                                        </span>
                                    @endif
                                    @if($selectedPost->category)
                                        <span class="badge text-body border">{{ $selectedPost->category->name }}</span>
                                    @endif
                                </div>
                                <h3 class="h5 mb-1">{{ $selectedPost->title }}</h3>
                                <div class="small text-body-secondary">
                                    {{ $selectedPost->identity?->display_name ?? __('Unknown') }}
                                    · {{ $selectedPost->created_at?->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-5 fw-semibold">{{ $selectedPost->net_score }}</div>
                                <div class="small text-body-secondary">{{ __('net score') }}</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            {!! $selectedPost->body_html !!}
                        </div>

                        @if($selectedPost->issues->isNotEmpty())
                            <div class="mt-4">
                                <div class="small text-uppercase fw-semibold text-body-secondary mb-2">{{ __('Linked issues') }}</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($selectedPost->issues as $issue)
                                        <a class="badge text-bg-light border text-decoration-none"
                                           href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}">
                                            {{ $issue->key }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <div class="small text-uppercase fw-semibold text-body-secondary mb-2">{{ __('Comments') }}</div>
                            <div class="d-flex flex-column gap-3">
                                @forelse($selectedPost->comments as $comment)
                                    <div class="border-top pt-3">
                                        <div class="small text-body-secondary mb-1">
                                            @if($comment->is_staff_reply)
                                                {{ $comment->staffUser?->name ?? __('Staff') }} ({{ __('Staff') }})
                                            @else
                                                {{ $comment->identity?->display_name ?? __('Unknown') }}
                                            @endif
                                            · {{ $comment->created_at?->diffForHumans() }}
                                        </div>
                                        <div>{!! $comment->body_html !!}</div>

                                        @foreach($comment->replies as $reply)
                                            <div class="border-start ps-3 mt-3">
                                                <div class="small text-body-secondary mb-1">
                                                    @if($reply->is_staff_reply)
                                                        {{ $reply->staffUser?->name ?? __('Staff') }} ({{ __('Staff') }})
                                                    @else
                                                        {{ $reply->identity?->display_name ?? __('Unknown') }}
                                                    @endif
                                                    · {{ $reply->created_at?->diffForHumans() }}
                                                </div>
                                                <div>{!! $reply->body_html !!}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="small text-body-secondary">{{ __('No comments yet.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </article>
                @else
                    <div class="rounded border bg-body-tertiary p-4 small text-body-secondary">
                        {{ __('Select a feedback post to read the discussion.') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
