<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\RedirectResponse;

final class MarkAllReadController
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        $user->unreadNotifications->markAsRead();

        return back();
    }
}
