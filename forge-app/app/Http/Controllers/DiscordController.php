<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issues\Issue;
use App\Models\Issues\IssueStatus;
use App\Models\Issues\IssueType;
use App\Models\User;
use App\Models\Projects\Project;
use App\Models\DiscordConfig as Discord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\DiscordAuthController as DiscordAuth;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class DiscordController
 *
 * This class is a controller for handling Discord interactions.
 */
class DiscordController extends Controller
{
    /**
     * Create a new issue in the database from a Discord submission
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Returns a JSON response with the status of the request
     */
    public function createIssue(Request $request)
    {
        // Validate the request/input
        $submission = $request->validate([
            'description' => 'required|string',
            'discordUser' => 'required|string',
            'content' => 'required|string',
            'product' => 'required|string',
            'issue_type' => 'required|string',
            'originChannel' => 'required|string',
        ]);

        // Try to find if the submitting User has a linked Forge account
        $user = User::where('discord_id', $submission['discordUser'])->first() ?? null;

        // Generate a unique issue title using the description and origin. Limit to 100 characters.
        $title = substr($submission['description'], 0, 50) . ' - ' . $submission['originChannel'];

        // TODO: Tie the issue to a product in the database to find the project to relate to.

        // Get the IssueStatus ID for the 'pending' status
        $statusId = IssueStatus::where('name', 'pending')->first();

        // Get the IssueType ID for the provided string
        $typeId = IssueType::where('name', $submission['issue_type'])->first();

        // Create a new issue
        $issue = Issue::create([
            'title' => 'Bug from Discord',
            'project_id' => 1,  // TODO: Define the project ID for the issue
            'description' => $submission['description'],
            'content' => $submission['content'],
            'issue_status_id' => $statusId,
            'issue_type_id' => $typeId,
            'created_by' => $submission['discordUser'],  // Track who submitted the issue TODO: Define the user ID as the Discord user may not be a registered user.
        ]);

        // Send a message to the Discord user that the issue was created
        $message = "Issue created with ID: {$issue->id} , an admin will review it shortly, thank you!";

        $this->sendDiscordMessage($submission['discordUser'], $message);

        // Return the response
        return response()->json(['id' => $issue->id], 201);
    }

    /**
     * Look up the current status of an issue
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function issueStatus($id)
    {
        // Find the requested Issue
        $issue = Issue::findOrFail($id);

        // Get the IssueStatus Id
        $statusId = $issue->issue_status_id;

        // Get the IssueStatus name
        $status = IssueStatus::findOrFail($statusId);

        // Return the response
        return response()->json(['status' => $status->name], 200);
    }

    /**
     * Approve an issue
     * Approving an issue will change the status to 'approved', allowing the issue to be worked on. Must also assign a responsible user and project.
     * The owner of the issue will be set to the user who approved the issue.
     *
     * @param mixed $id
     * @param User $responsibleUser
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveIssue($id, User $responsibleUser, Project $project)
    {
        $issue = Issue::findOrFail($id);

        // Get the IssueStatus ID for the 'approved' status
        $statusId = IssueStatus::where('name', 'approved')->first();

        // Set the issue status to 'approved'
        $issue->issue_status_id = $statusId;

        // Get the authenticated user
        $user = Auth::user();

        // Assign ownership of the issue to the user who approved it
        $issue->owner_id = $user->id;

        // Get the responsible user and project
        $issue->responsible_id = $responsibleUser->id;
        $issue->project_id = $project->id;

        // Save the changes
        $issue->save();

        // Send a message to the Discord user that the issue was approved
        $message = "Your issue #{$issue->id} has been approved and assigned. If we need more information, we will reach out to you. Thank you!";

        $this->sendDiscordMessage($issue->created_by, $message);

        return response()->json(['message' => 'Issue approved']);
    }

    /**
     * Reject an issue
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectIssue($id)
    {
        $issue = Issue::findOrFail($id);

        // Get the IssueStatus ID for the 'rejected' status
        $statusId = IssueStatus::where('name', 'rejected')->first();

        // Set the issue status to 'rejected'
        $issue->issue_status_id = $statusId;

        // Get the authenticated user
        $user = Auth::user();

        // Assign ownership of the issue to the user who rejected it
        $issue->owner_id = $user->id;

        // Save the changes
        $issue->save();

        // Send a message to the Discord user that the issue was rejected
        $message = "Your issue #{$issue->id} has been rejected. If you have any questions, please reach out to us. Thank you!";

        $this->sendDiscordMessage($issue->created_by, $message);

        // Return the response
        return response()->json(['message' => 'Issue rejected']);
    }

    /**
     * Send a message to a Discord user
     *
     * @param string $discordUsername
     * @param string $message
     * @return void
     * @throws Exception
     */
    protected function sendDiscordMessage($discordUsername, $message)
    {
        // Path to the bot script
        $botPath = base_path('bot/discord-bot.js');

        // Initialize the Process with command and arguments
        $process = new Process(['node', $botPath, $discordUsername, $message]);

        try {
            $process->setTimeout(60);  // Set a timeout of 60 seconds
            // Run the process and wait for it to finish
            $process->mustRun();

            // Log success
            Log::info("Message sent to {$discordUsername}: {$message}");
        } catch (ProcessFailedException $e) {
            // Log any errors
            Log::error("Failed to send message to {$discordUsername}: " . $e->getMessage());

            // Throw an exception
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            // Catch any other exceptions and log them
            Log::error("Error sending Discord message: {$e->getMessage()}");

            // Throw an exception
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Trigger the Discord bot to send a notification to a User.
     *
     * @param mixed $userId
     * @param mixed $message
     * @return void
     * @throws \Exception
     */
    protected function sendDiscordNotification($userId, $message)
    {
        // Path to the bot script
        $botPath = base_path('bot/discord-bot.js');

        // Initialize the Process with command and arguments to send a notification
        $process = new Process(['node', $botPath, $userId, $message]);

        try {
            $process->setTimeout(60);  // Set a timeout of 60 seconds
            // Run the process and wait for it to finish
            $process->mustRun();

            // Log success
            Log::info("Notification sent to {$userId}: {$message}");
        } catch (ProcessFailedException $e) {
            // Log any errors
            Log::error("Failed to send notification to {$userId}: " . $e->getMessage());

            // Throw an exception
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            // Catch any other exceptions and log them
            Log::error("Error sending Discord notification: {$e->getMessage()}");

            // Throw an exception
            throw new \Exception($e->getMessage());
        }
    }
}
