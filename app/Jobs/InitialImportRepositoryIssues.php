<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssueStatus;
use App\Models\ProjectRepository;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
            ->with([
                'project',
                'repository',
                'repository.statusMappings.status',
                'integrator.socialAccounts',
            ])
            ->findOrFail($this->projectRepositoryId);

        $project = $link->project;
        $repo    = $link->repository;

        // --- Resolve an access token (link token OR integrator's SocialAccount) ---
        $token = $link->token;
        if (!$token && $link->integrator) {
            $token = optional(
                $link->integrator->socialAccounts->firstWhere('provider', $repo->provider)
            )->token;
        }

        if (!$token) {
            $link->update([
                'initial_import_started_at'  => now(),
                'last_sync_status'           => 'error',
                'last_sync_error'            => 'No OAuth/PAT token available for the integrator or link.',
                'initial_import_finished_at' => now(),
            ]);
            return;
        }

        $link->update([
            'initial_import_started_at' => now(),
            'last_sync_status'          => null,
            'last_sync_error'           => null,
        ]);

        try {
            $factory  = app('repo-provider-factory');
            $provider = $factory($repo->provider);

            $issues = $provider->fetchAllIssues($repo, $token); // may throw

            // Map external states -> local IssueStatus
            $map = $repo->statusMappings->keyBy(fn ($m) => strtolower($m->external_state));

            foreach ($issues as $i) {
                $state = strtolower($i['state'] ?? '');
                $statusId = optional($map->get($state))->issue_status_id;

                // Fallback: open -> first non-done; closed -> any done
                if (!$statusId) {
                    $statusId = IssueStatus::query()
                        ->when(
                            $state === 'closed',
                            fn ($q) => $q->where('is_done', true),
                            fn ($q) => $q->where('is_done', false)
                        )
                        ->orderBy('order')
                        ->value('id');
                }

                $issue = Issue::query()->create([
                    'project_id'      => $project->id,
                    'summary'         => $i['title'],
                    'description'     => $i['body'] ?? null,
                    'issue_status_id' => $statusId,
                    'created_at'      => $i['created_at'] ?? now(),
                    'updated_at'      => $i['updated_at'] ?? now(),
                ]);

                IssueExternalRef::query()->create([
                    'issue_id'          => $issue->id,
                    'repository_id'     => $repo->id,
                    'provider'          => $repo->provider,
                    'external_issue_id' => $i['external_issue_id'],
                    'number'            => (int) $i['number'],
                    'url'               => $i['url'] ?? null,
                    'state'             => $i['state'] ?? null,
                    'payload'           => $i['raw'] ?? null,
                ]);

                // Assignee mapping (only if the user connected their provider account)
                if (!empty($i['assignee_login'])) {
                    /** @var User|null $assignee */
                    $assignee = User::query()
                        ->whereHas('socialAccounts', function ($q) use ($repo, $i) {
                            $q->where('provider', $repo->provider)
                                ->where('nickname', $i['assignee_login']);
                        })->first();

                    if ($assignee) {
                        $issue->assignee()->associate($assignee)->save();
                    }
                }

                // Reporter mapping (same rule)
                if (!empty($i['reporter_login'])) {
                    /** @var User|null $reporter */
                    $reporter = User::query()
                        ->whereHas('socialAccounts', function ($q) use ($repo, $i) {
                            $q->where('provider', $repo->provider)
                                ->where('nickname', $i['reporter_login']);
                        })->first();

                    if ($reporter) {
                        $issue->reporter()->associate($reporter)->save();
                    }
                }
            }

            $link->update([
                'initial_import_finished_at' => now(),
                'last_sync_status'           => 'ok',
                'last_sync_error'            => null,
            ]);
        } catch (\Throwable $e) {
            $link->update([
                'last_sync_status'           => 'error',
                'last_sync_error'            => mb_strimwidth($e->getMessage(), 0, 8000),
                'initial_import_finished_at' => now(),
            ]);
            report($e);
        }
    }
}
