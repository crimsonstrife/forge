<?php

namespace App\Listeners;

use App\Http\Controllers\Auth\DiscordAuthController;
use App\Events\RoleAssigned;
use App\Events\RoleRemoved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncForgeRolesToDiscord
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $discordController = new DiscordAuthController;

        $discordController->syncForgeRolesToDiscord($event->user->id);
    }
}
