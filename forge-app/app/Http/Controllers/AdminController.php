<?php

namespace App\Http\Controllers;

use App\Models\DiscordConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    /**
     * Show the Discord settings form in Filament Admin Panel.
     * This method retrieves the current Discord settings from the database.
     */
    public function showDiscordSettings()
    {
        // Get the existing DiscordConfig record (there should only be one)
        $config = DiscordConfig::first();

        return view('admin.discord_settings', compact('config'));
    }

    /**
     * Update Discord settings in the database.
     * Encrypt the sensitive fields before saving (bot_token and client_secret).
     */
    public function updateDiscordSettings(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'bot_token' => 'required|string',
            'guild_id' => 'required|string',
            'redirect_uri' => 'required|url',
            'role_mappings' => 'nullable|json',
            'channel_mappings' => 'nullable|json',
        ]);

        // Always get the first record or create a new one if it doesn't exist
        $config = DiscordConfig::firstOrNew(['id' => 1]);

        // Update the configuration fields
        $config->client_id = $request->input('client_id');
        $config->client_secret = encrypt($request->input('client_secret')); // Encrypt the client_secret
        $config->bot_token = encrypt($request->input('bot_token')); // Encrypt the bot_token
        $config->guild_id = $request->input('guild_id');
        $config->redirect_uri = $request->input('redirect_uri');

        // Decode the JSON input for role and channel mappings
        $config->role_mappings = json_decode($request->input('role_mappings'), true);
        $config->channel_mappings = json_decode($request->input('channel_mappings'), true);

        // Save the configuration to the database
        $config->save();

        return redirect()->back()->with('success', 'Discord settings updated successfully!');
    }
}
