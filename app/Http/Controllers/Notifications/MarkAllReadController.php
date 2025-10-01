<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\RedirectResponse;

final class MarkAllReadController
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();
        $user?->unreadNotifications->markAsRead();

        return back();
    }
}
