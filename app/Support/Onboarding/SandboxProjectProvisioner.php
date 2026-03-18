<?php

namespace App\Support\Onboarding;

use App\Enums\ProjectStage;
use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use App\Services\Projects\ProjectSchemeSeeder;
use App\Support\Keys\ProjectKeyGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class SandboxProjectProvisioner
{
    public function __construct(
        private ProjectSchemeSeeder $schemeSeeder,
    ) {
    }

    /**
     * @return array{project: Project, issue: Issue}
     */
    public function sandboxFor(User $user): array
    {
        return DB::transaction(function () use ($user): array {
            $project = $this->findProject($user) ?? $this->createProject($user);

            $this->schemeSeeder->seed($project);

            $issue = $this->ensureIssues($project, $user);

            return [
                'project' => $project->fresh(),
                'issue' => $issue->fresh(),
            ];
        });
    }

    private function findProject(User $user): ?Project
    {
        $project = Project::withTrashed()
            ->where('lead_id', $user->getKey())
            ->get()
            ->first(fn (Project $project) => data_get($project->settings, 'onboarding.sandbox') === true);

        if ($project?->trashed()) {
            $project->restore();
        }

        return $project;
    }

    private function createProject(User $user): Project
    {
        $name = sprintf('Forge Sandbox %s', strtoupper(substr($user->getKey(), 0, 6)));

        $project = new Project();
        $project->name = $name;
        $project->key = app(ProjectKeyGenerator::class)->uniqueForName($name, 4);
        $project->description = __('A private sample project for onboarding tours. It is safe to edit or delete once you are done exploring Forge.');
        $project->lead_id = $user->getKey();
        $project->stage = ProjectStage::Planning;
        $project->settings = [
            'onboarding' => [
                'sandbox' => true,
            ],
        ];
        $project->save();

        $this->attachOwner($project, $user);

        return $project;
    }

    private function ensureIssues(Project $project, User $user): Issue
    {
        $project->refresh();

        $primaryIssueId = data_get($project->settings, 'onboarding.primary_issue_id');

        if (filled($primaryIssueId)) {
            $existingIssue = $project->issues()->whereKey($primaryIssueId)->first();

            if ($existingIssue instanceof Issue) {
                return $existingIssue;
            }
        }

        $defaults = $this->defaults($project);

        $primaryIssue = $project->issues()->create([
            'issue_type_id' => $defaults['task_type_id'],
            'issue_status_id' => $defaults['in_progress_status_id'],
            'issue_priority_id' => $defaults['high_priority_id'],
            'reporter_id' => $user->getKey(),
            'assignee_id' => $user->getKey(),
            'story_points' => 5,
            'estimate_minutes' => 180,
            'summary' => __('Sample: Plan the Forge onboarding rollout'),
            'description' => __('Use this sample issue to explore status changes, time tracking, notes, comments, attachments, and sub-issues without touching a live project.'),
        ]);

        $project->issues()->create([
            'project_id' => $project->getKey(),
            'parent_id' => $primaryIssue->getKey(),
            'issue_type_id' => $defaults['subtask_type_id'],
            'issue_status_id' => $defaults['done_status_id'],
            'issue_priority_id' => $defaults['medium_priority_id'],
            'reporter_id' => $user->getKey(),
            'assignee_id' => $user->getKey(),
            'story_points' => 2,
            'estimate_minutes' => 45,
            'summary' => __('Sample: Draft the first-run invitation copy'),
            'description' => __('A completed sub-issue so the issue detail view can demonstrate rollups and progress.'),
        ]);

        $project->issues()->create([
            'project_id' => $project->getKey(),
            'parent_id' => $primaryIssue->getKey(),
            'issue_type_id' => $defaults['subtask_type_id'],
            'issue_status_id' => $defaults['todo_status_id'],
            'issue_priority_id' => $defaults['medium_priority_id'],
            'reporter_id' => $user->getKey(),
            'assignee_id' => null,
            'story_points' => 3,
            'estimate_minutes' => 60,
            'summary' => __('Sample: Add a project walkthrough with stable anchors'),
            'description' => __('An open sub-issue so the tour has pending work to point at.'),
        ]);

        $project->issues()->create([
            'project_id' => $project->getKey(),
            'issue_type_id' => $defaults['bug_type_id'],
            'issue_status_id' => $defaults['review_status_id'],
            'issue_priority_id' => $defaults['medium_priority_id'],
            'reporter_id' => $user->getKey(),
            'assignee_id' => $user->getKey(),
            'story_points' => 1,
            'estimate_minutes' => 30,
            'summary' => __('Sample: Verify the tour after completion'),
            'description' => __('A second top-level issue so the project overview has more than one item to summarize.'),
        ]);

        $primaryIssue->comments()->create([
            'user_id' => $user->getKey(),
            'body' => __('This comment is here so the issue detail page has a real discussion thread to point at during onboarding.'),
        ]);

        $project->settings = array_replace_recursive($project->settings ?? [], [
            'onboarding' => [
                'sandbox' => true,
                'primary_issue_id' => $primaryIssue->getKey(),
            ],
        ]);
        $project->save();

        return $primaryIssue;
    }

    /**
     * @return array<string, int>
     */
    private function defaults(Project $project): array
    {
        return [
            'task_type_id' => (int) ($project->defaultTypeId() ?: IssueType::query()->where('key', 'TASK')->value('id')),
            'subtask_type_id' => (int) (IssueType::query()->where('key', 'SUBTASK')->value('id') ?: $project->defaultTypeId()),
            'bug_type_id' => (int) (IssueType::query()->where('key', 'BUG')->value('id') ?: $project->defaultTypeId()),
            'todo_status_id' => (int) (IssueStatus::query()->where('key', 'TODO')->value('id') ?: $project->initialStatusId()),
            'in_progress_status_id' => (int) (IssueStatus::query()->where('key', 'INPROGRESS')->value('id') ?: $project->initialStatusId()),
            'review_status_id' => (int) (IssueStatus::query()->where('key', 'INREVIEW')->value('id') ?: $project->initialStatusId()),
            'done_status_id' => (int) (IssueStatus::query()->where('key', 'DONE')->value('id') ?: $project->initialStatusId()),
            'high_priority_id' => (int) (IssuePriority::query()->where('key', 'HIGH')->value('id') ?: $project->defaultPriorityId()),
            'medium_priority_id' => (int) (IssuePriority::query()->where('key', 'MEDIUM')->value('id') ?: $project->defaultPriorityId()),
        ];
    }

    private function attachOwner(Project $project, User $user): void
    {
        $attributes = [
            'project_id' => $project->getKey(),
            'user_id' => $user->getKey(),
        ];

        if (DB::table('project_user')->where($attributes)->exists()) {
            DB::table('project_user')
                ->where($attributes)
                ->update([
                    'role' => 'Owner',
                    'updated_at' => now(),
                ]);

            return;
        }

        $values = [
            ...$attributes,
            'role' => 'Owner',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('project_user', 'id')) {
            $values['id'] = (string) Str::uuid();
        }

        DB::table('project_user')->insert($values);
    }
}
