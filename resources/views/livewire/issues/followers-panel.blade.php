<div class="card shadow-sm">
    <div class="card-body d-flex flex-column gap-3">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h4 class="h6 mb-1">{{ __('Followers') }}</h4>
                <div class="small text-body-secondary">
                    {{ trans_choice(':count person is following this issue.', $followers->count(), ['count' => $followers->count()]) }}
                </div>
            </div>

            @auth
                <button type="button"
                        class="btn btn-sm {{ $isFollowing ? 'btn-outline-secondary' : 'btn-primary' }}"
                        wire:click="toggle">
                    {{ $isFollowing ? __('Unfollow') : __('Follow') }}
                </button>
            @endauth
        </div>

        @if($followers->isEmpty())
            <div class="small text-body-secondary">{{ __('No followers yet.') }}</div>
        @else
            <div class="d-flex flex-column gap-2">
                @foreach($followers as $follower)
                    <div class="d-flex align-items-center gap-2">
                        <x-avatar :src="$follower->profile_photo_url" :name="$follower->name" preset="sm"/>
                        <div class="small">
                            <div class="fw-semibold">{{ $follower->name }}</div>
                            @if($follower->email)
                                <div class="text-body-secondary">{{ $follower->email }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
