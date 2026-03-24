<?php

use Illuminate\Http\Request;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('notifications.index');
middleware(['auth','verified']);

render(function (View $view, Request $request) {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    if ($request->isMethod('post') && $request->input('action') === 'mark-all-read') {
        $user->unreadNotifications->markAsRead();
        return redirect()->route('notifications.index');
    }

    $filter = $request->string('filter')->toString(); // all|unread
    $query = $user->notifications()->latest();

    if ($filter === 'unread') {
        $query->whereNull('read_at');
    }

    $notifications = $query->paginate(20);

    $view->with(compact('notifications', 'filter'));
});
?>

<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">{{ __('Notifications') }}</h1></x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group">
                <a class="btn btn-sm {{ $filter !== 'unread' ? 'btn-primary' : 'btn-outline-primary' }}"
                   href="{{ route('notifications.index') }}">{{ __('All') }}</a>
                <a class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}"
                   href="{{ route('notifications.index', ['filter' => 'unread']) }}">{{ __('Unread') }}</a>
            </div>
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">{{ __('Mark all as read') }}</button>
            </form>
        </div>

        <div class="list-group">
            @forelse($notifications as $n)
                @php
                    $data = $n->data ?? [];
                    $title = $data['title'] ?? str(class_basename($n->type))->headline()->toString();
                @endphp
                <a class="list-group-item list-group-item-action d-flex justify-content-between {{ $n->read_at ? '' : 'list-group-item-info' }}"
                   href="{{ $data['url'] ?? '#' }}"
                   @if(!$n->read_at) aria-unread="true" @endif>
                    <div class="d-flex align-items-center">
                        @if(!$n->read_at)
                            <span class="me-2" aria-label="{{ __('Unread notification') }}" title="{{ __('Unread notification') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16" style="vertical-align: middle;">
                                    <circle cx="8" cy="8" r="6"/>
                                </svg>
                            </span>
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $title }}</div>
                            @if(isset($data['summary']))
                                <div class="small text-muted">{{ $data['summary'] }}</div>
                            @endif
                        </div>
                    </div>
                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </a>
            @empty
                <div class="list-group-item text-muted">{{ __('No notifications found.') }}</div>
            @endforelse
        </div>

        <div class="mt-3">{{ $notifications->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
