<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssueStatus;
use App\Models\IssueStatusMapping;
use App\Models\ProjectRepository;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

final class InitialImportRepositoryIssues implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(public string $projectRepositoryId)
    {
    }

    public function handle(): void
    {
        /** @var ProjectRepository $link */
        $link = ProjectRepository::query()
            ->with(['project','repository','repository.statusMappings.status'])
            ->findOrFail($this->projectRepositoryId);

        $project   = $link->project;
        $repo      = $link->repository;
        $token     = (string) $link->token;

        $factory   = app('repo-provider-factory');
        $provider  = $factory($repo->provider);

        $link->update(['initial_import_started_at' => now()]);

        $issues = $provider->fetchAllIssues($repo, $token);

        // quick mapping helpers
        $map = $repo->statusMappings->keyBy(fn ($m) => strtolower($m->external_state));

        foreach ($issues as $i) {
            $statusId = optional($map->get(strtolower($i['state']) ?? ''))->issue_status_id;

            // Fallback suggestion: open -> first not-done; closed -> any done
            if (!$statusId) {
                $statusId = IssueStatus::query()
                    ->when(
                        ($i['state'] ?? '') === 'closed',
                        fn ($q) => $q->where('is_done', true),
                        fn ($q) => $q->where('is_done', false)
                    )
                    ->orderBy('order')
                    ->value('id');
            }

            $issue = Issue::query()->create([
                'project_id'   => $project->id,
                'summary'      => $i['title'],
                'description'  => $i['body'] ?? null,
                'issue_status_id' => $statusId,
                // You can map priorities/types later via labels if you want
                'created_at'   => $i['created_at'] ?? now(),
                'updated_at'   => $i['updated_at'] ?? now(),
            ]);

            IssueExternalRef::query()->create([
                'issue_id'          => $issue->id,
                'repository_id'     => $repo->id,
                'provider'          => $repo->provider,
                'external_issue_id' => $i['external_issue_id'],
                'number'            => (int)$i['number'],
                'url'               => $i['url'] ?? null,
                'state'             => $i['state'] ?? null,
                'payload'           => $i['raw'] ?? null,
            ]);

            // Assignee/reporter mapping (only if the user has connected their account)
            $assigneeLogin = $i['assignee_login'] ?? null;
            if ($assigneeLogin) {
                $user = User::query()
                    ->whereHas('socialAccounts', function ($q) use ($repo, $assigneeLogin) {
                        $q->where('provider', $repo->provider)->where('nickname', $assigneeLogin);
                    })->first();

                if ($user) {
                    $issue->assignee()->associate($user)->save();
                }
            }

            $reporterLogin = $i['reporter_login'] ?? null;
            if ($reporterLogin) {
                $user = User::query()
                    ->whereHas('socialAccounts', function ($q) use ($repo, $reporterLogin) {
                        $q->where('provider', $repo->provider)->where('nickname', $reporterLogin);
                    })->first();

                if ($user) {
                    $issue->reporter()->associate($user)->save();
                }
            }
        }

        $link->update(['initial_import_finished_at' => now(), 'last_sync_status' => 'ok']);
    }
}
