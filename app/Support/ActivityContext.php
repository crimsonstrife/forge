<?php
namespace App\Support;

use Illuminate\Support\Facades\Auth;

final class ActivityContext
{
    public static function base(): array
    {
        return [
            'team_id'    => Auth::user()?->currentTeam?->id,
            'user_id'    => Auth::id(),
            'ip'         => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];
    }
}
