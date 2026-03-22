@php /** @var \Illuminate\Notifications\DatabaseNotification[] $latest */ @endphp

<div class="dropdown">
    <button class="btn position-relative" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <wa-icon family="solid" name="bell"></wa-icon>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
                <span class="visually-hidden">{{ __('unread notifications') }}</span>
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown" style="min-width: 22rem;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <span class="small text-muted">{{ __('Notifications') }}</span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="btn btn-link btn-sm p-0">{{ __('Mark all as read') }}</button>
            @endif
        </div>

        @forelse($latest as $n)
            @php
                $data = $n->data ?? [];
                $title = $data['title'] ?? ($n->type ? str(class_basename($n->type))->headline()->toString() : __('Notification'));
                $url = $data['url'] ?? null;
                $isUnread = is_null($n->read_at);
            @endphp

            <a @class(['dropdown-item d-flex flex-column gap-1', 'bg-secondary' => $isUnread]) href="{{ $url ?? '#' }}">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">{{ __($title) }}</span>
                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </div>
                @if(isset($data['summary']))
                    <small class="text-muted text-wrap">{{ $data['summary'] }}</small>
                @endif
            </a>
        @empty
            <div class="px-3 py-3 text-muted small">{{ __('No notifications yet.') }}</div>
        @endforelse

        <div class="border-top">
            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">{{ __('View all') }}</a>
        </div>
    </div>
</div>

{{-- Echo subscription --}}
@auth
    <script>
        (() => {
            // Guard: Echo may not be present in all pages
            if (!window.Echo) {
                return;
            }

            const userId = @json(auth()->id(), JSON_THROW_ON_ERROR);
            const channel = `private-App.Models.User.${userId}`;

            window.Echo.private(channel)
                .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (e) => {
                    // When any broadcast notification arrives for this user, refresh list
                    window.Livewire.dispatch('notification:refresh');
                });
        })();
    </script>
@endauth
