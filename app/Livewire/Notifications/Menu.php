<?php

namespace App\Livewire\Notifications;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Renders a bell with unread count + latest notifications.
 *
 * Props: none (uses auth()->user()).
 */
class Menu extends Component
{
    public int $unreadCount = 0;

    /** @var array<int, DatabaseNotification> */
    public array $latest = [];

    public function mount(): void
    {
        $this->refreshList();
    }

    #[On('notification:refresh')]
    public function refreshList(): void
    {
        $user = auth()->user();
        if (! $user) {
            $this->unreadCount = 0;
            $this->latest = [];
            return;
        }

        $this->unreadCount = $user->unreadNotifications()->count();
        $this->latest = $user->notifications()->latest()->limit(10)->get()->all();
    }

    public function markAllRead(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->refreshList();
    }

    public function render(): View
    {
        return view('livewire.notifications.menu');
    }
}
