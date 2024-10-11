<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Auth\Role;
use App\Models\DiscordConfig as Discord;

/**
 * Class DiscordAuthController
 *
 * This class is a controller for handling Discord account linking.
 */
class DiscordAuthController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToDiscord()
    {
        return Socialite::driver('discord')->redirect();
    }

    /**
     * Handle the Discord callback and link the Discord account to the authenticated user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleDiscordCallback()
    {
        $discordUser = Socialite::driver('discord')->user();

        // Get the authenticated user
        $user = Auth::user();

        // Get the User ID
        $thisUserId = $user->id;

        // Check if the Discord ID is already linked to another user
        $existingUser = User::where('discord_id', $discordUser->id)->first();

        if ($existingUser) {
            return redirect('/profile')->with('error', 'Discord account already linked to another user!');
        }

        // Fetch the roles from Discord (use Node.js bot to fetch)
        $roles = $this->fetchDiscordRoles($discordUser->id);

        // Get the user object
        $thisUser = User::find($thisUserId);

        // Link the Discord ID to the user
        $thisUser->discord_id = $discordUser->id;

        // Link the Discord Roles to the user
        $thisUser->discord_roles = $roles;

        // Save the changes
        $thisUser->save();

        // Sync the Discord roles with Forge roles
        $this->syncDiscordRolesToForge($user->id);

        // Redirect back to the profile page with a success message if the Discord account was linked successfully
        if ($thisUser->discord_id === $discordUser->id) {
            return redirect('/profile')->with('success', 'Discord account linked successfully!');
        }
    }

    /**
     * Unlink the Discord account from the authenticated user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlinkDiscord()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the User ID
        $thisUserId = $user->id;

        // Get the user object
        $thisUser = User::find($thisUserId);

        // Check if the user has a Discord ID linked
        if (!$thisUser->discord_id) {
            return redirect('/profile')->with('error', 'No Discord account linked to this user!');
        }

        // Unlink the Discord ID from the user
        $thisUser->discord_id = null;

        // Save the changes
        $thisUser->save();

        // Sync the Discord roles with Forge roles
        $this->syncDiscordRolesToForge($user->id);

        // Redirect back to the profile page with a success message if the Discord account was unlinked successfully
        return redirect('/profile')->with('success', 'Discord account unlinked successfully!');
    }

    /**
     * Check if a User has a linked Discord account
     */
    protected function isUserDiscordLinked($userId)
    {
        $user = User::find($userId);
        return $user && $user->discord_id;  // Check if the user has linked their Discord account
    }

    /**
     * Get the Discord ID from a User
     * @param mixed $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getUserDiscordId($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json(['discord_id' => $user->discord_id]);
    }

    /**
     * Helper function to fetch Discord roles using the bot
     * @param mixed $discordId
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function fetchDiscordRoles($discordId)
    {
        $botPath = base_path('bot/discordBot.js');
        $command = "node {$botPath} fetchUserRoles {$discordId}";
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            return json_decode(implode("\n", $output), true);
        }

        return [];
    }

    /**
     * Helper function to sync Discord roles with Forge
     * @param int $userID
     * @return void
     */
    protected function syncDiscordRolesToForge($userId)
    {
        $user = User::findOrFail($userId);

        // Define the mapping between Forge roles and Discord roles
        $forgeToDiscordRoleMapping = [
            //'staff_role_in_forge' => 'staff_role_in_discord',  // Forge role => Discord role
            //'admin_role_in_forge' => 'admin_role_in_discord',
            // Add more mappings as needed
        ];

        // Fetch the user's roles in Forge
        $forgeRoles = $user->roles->pluck('name')->toArray();

        // Fetch Discord roles for the user and assign them based on Forge roles
        foreach ($forgeRoles as $forgeRole) {
            if (isset($forgeToDiscordRoleMapping[$forgeRole])) {
                $discordRole = $forgeToDiscordRoleMapping[$forgeRole];
                $this->assignDiscordRole($user->discord_id, $discordRole);
            }
        }
    }

    /**
     * This function calls the Discord bot to assign a role in Discord
     */
    protected function assignDiscordRole($discordId, $discordRoleName)
    {
        // Use the bot to assign the role via Discord API
        $botPath = base_path('bot/discordBot.js');
        $command = "node {$botPath} assignRole {$discordId} '{$discordRoleName}'";
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            Log::info("Role '{$discordRoleName}' assigned to Discord user ID: {$discordId}");
        } else {
            Log::error("Failed to assign role '{$discordRoleName}' to Discord user ID: {$discordId}");
        }
    }
}
