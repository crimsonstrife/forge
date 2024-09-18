<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ProjectUser;
use App\Models\Issues\Issue;
use App\Models\Issues\IssuePriority;
use App\Models\Issues\IssueStatus;
use App\Models\Issues\IssueType;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ImportJiraIssues class.
 *
 * This class represents a job for importing Jira issues into the application.
 * It implements the ShouldQueue interface to allow the job to be queued for background processing.
 */
class ImportJiraIssues implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;

    private $issues;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($issues, $user)
    {
        $this->issues = $issues;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->issues && sizeof($this->issues)) {
            foreach ($this->issues as $issue) {
                $projectDetails = $issue->fields->project;
                $issueData = $issue->fields;

                $project = Project::where('name', $projectDetails->name)->first();
                if (!$project) {
                    $project = Project::create([
                        'name' => $projectDetails->name,
                        'description' => __('Project imported from Jira, project key:') . $projectDetails->key,
                        'status_id' => ProjectStatus::where('is_default', true)->first()->id,
                        'owner_id' => $this->user->id,
                        'issue_prefix' => $projectDetails->key
                    ]);

                    ProjectUser::create([
                        'project_id' => $project->id,
                        'user_id' => $this->user->id,
                        'role' => config('system.projects.affectations.roles.can_manage')
                    ]);
                }

                Issue::create([
                    'name' => $issueData->summary,
                    'content' => $issueData->description ?? __('No content found in jira issue'),
                    'owner_id' => $this->user->id,
                    'status_id' => IssueStatus::where('is_default', true)->first()->id,
                    'project_id' => $project->id,
                    'type_id' => IssueType::where('is_default', true)->first()->id,
                    'priority_id' => IssuePriority::where('is_default', true)->first()->id,
                ]);
            }
            FilamentNotification::make()
                ->title(__('Jira importation'))
                ->icon('fa-regular fa-cloud-arrow-down')
                ->body(__('Jira issues successfully imported'))
                ->sendToDatabase($this->user);
        }
    }
}
