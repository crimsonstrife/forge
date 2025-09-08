<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\IssuePriority;
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

        $token = $link->token ?: optional(
            $link->integrator?->socialAccounts->firstWhere('provider', $repo->provider)
        )->token;

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

            $issues = $provider->fetchAllIssues($repo, $token);

            $statusMap = $repo->statusMappings->keyBy(fn ($m) => strtolower($m->external_state));

            [$defaultTypeId, $byKey, $byName, $byTier] = $this->prepareIssueTypeLookups();
            [$defaultPriorityId, $prioByKey, $prioByName] = $this->preparePriorityLookups();

            foreach ($issues as $i) {
                $state = strtolower($i['state'] ?? '');

                $statusId = optional($statusMap->get($state))->issue_status_id;
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

                $issueTypeId = $this->inferIssueTypeIdFromLabels(
                    $i['labels'] ?? [],
                    $byKey,
                    $byName,
                    $byTier,
                    $defaultTypeId
                );

                $issuePriorityId = $this->inferPriorityIdFromLabels(
                    $i['labels'] ?? [],
                    $prioByKey,
                    $prioByName,
                    $defaultPriorityId
                );

                $issue = Issue::query()->create([
                    'project_id'      => $project->id,
                    'summary'         => $i['title'],
                    'description'     => $i['body'] ?? null,
                    'issue_status_id' => $statusId,
                    'issue_type_id'   => $issueTypeId,
                    'issue_priority_id' => $issuePriorityId,
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

    /**
     * @return array{0:string,1:array<string,string>,2:array<string,string>,3:array<string,string>}
     */
    private function prepareIssueTypeLookups(): array
    {
        $types = IssueType::query()->get(['id', 'key', 'name', 'tier', 'is_default']);

        $default = $types->firstWhere('is_default', true)
            ?? $types->firstWhere('key', 'TASK')
            ?? $types->first();

        if (!$default) {
            throw new \RuntimeException('No IssueTypes exist; seed IssueTypes before importing.');
        }

        $byKey  = [];
        $byName = [];
        $byTier = [];
        foreach ($types as $t) {
            if ($t->key) {
                $byKey[strtolower((string) $t->key)]   = (string) $t->id;
            }
            if ($t->name) {
                $byName[strtolower((string) $t->name)] = (string) $t->id;
            }
            if ($t->tier) {
                $byTier[strtolower($t->tier->value)]   = (string) $t->id;
            }
        }

        return [(string) $default->id, $byKey, $byName, $byTier];
    }

    /**
     * @param array<int,string> $labels
     */
    private function inferIssueTypeIdFromLabels(
        array $labels,
        array $byKey,
        array $byName,
        array $byTier,
        string $defaultId
    ): string {
        $synonyms = [
            'bug'     => ['bug', 'defect'],
            'feature' => ['feature', 'enhancement', 'fr'],
            'task'    => ['task', 'chore'],
            'story'   => ['story', 'user story'],
            'epic'    => ['epic'],
            'subtask' => ['subtask', 'sub-task'],
        ];

        foreach ($labels as $label) {
            $l = strtolower((string) $label);

            if (isset($byKey[$l])) {
                return $byKey[$l];
            }
            if (isset($byName[$l])) {
                return $byName[$l];
            }

            foreach ($synonyms as $canonical => $alts) {
                if ($l === $canonical || in_array($l, $alts, true)) {
                    $canonKey = strtolower($canonical);
                    if (isset($byKey[$canonKey])) {
                        return $byKey[$canonKey];
                    }
                    if (isset($byTier[$canonKey])) {
                        return $byTier[$canonKey];
                    }
                }
            }
        }

        return $defaultId;
    }

    /**
     * Priority helpers
     * @return array{0:string,1:array<string,string>,2:array<string,string>}
     */
    private function preparePriorityLookups(): array
    {
        // Your seeder defines keys: HIGHEST, HIGH, MEDIUM, LOW, LOWEST
        $priorities = IssuePriority::query()->get(['id', 'key', 'name']);

        // default: MEDIUM if exists, else first
        $default = $priorities->firstWhere('key', 'MEDIUM') ?? $priorities->first();

        if (!$default) {
            throw new \RuntimeException('No IssuePriorities exist; seed IssuePriorities before importing.');
        }

        $byKey  = [];
        $byName = [];
        foreach ($priorities as $p) {
            if ($p->key) {
                $byKey[strtolower((string) $p->key)]   = (string) $p->id;
            }
            if ($p->name) {
                $byName[strtolower((string) $p->name)] = (string) $p->id;
            }
        }

        return [(string) $default->id, $byKey, $byName];
    }

    /**
     * Infer priority from labels like: p0/p1/p2, blocker, critical, high, medium, low, lowest, trivial, etc.
     * Falls back to $defaultId when no match.
     * @param array<int,string> $labels
     */
    private function inferPriorityIdFromLabels(
        array $labels,
        array $byKey,
        array $byName,
        string $defaultId
    ): string {
        // Map synonyms → canonical priority keys defined in your seeder
        $synonyms = [
            'HIGHEST' => ['p0', 'blocker', 'critical', 'urgent', 'sev1', 'highest'],
            'HIGH'    => ['p1', 'high', 'important', 'sev2'],
            'MEDIUM'  => ['p2', 'medium', 'normal', 'default'],
            'LOW'     => ['p3', 'low', 'minor', 'sev4'],
            'LOWEST'  => ['p4', 'lowest', 'trivial'],
        ];

        foreach ($labels as $label) {
            $l = strtolower((string) $label);

            // direct by key or name (case-insensitive)
            if (isset($byKey[$l])) {
                return $byKey[$l];
            }
            if (isset($byName[$l])) {
                return $byName[$l];
            }

            // numeric patterns like "P0", "p1", "prio: high"
            if (preg_match('/^p([0-4])$/i', $l, $m)) {
                $map = ['0' => 'HIGHEST', '1' => 'HIGH', '2' => 'MEDIUM', '3' => 'LOW', '4' => 'LOWEST'];
                $k = strtolower($map[$m[1]]);
                if (isset($byKey[$k])) {
                    return $byKey[$k];
                }
            }

            // synonyms
            foreach ($synonyms as $canonicalKey => $alts) {
                foreach ($alts as $alt) {
                    if ($l === strtolower($alt)) {
                        $k = strtolower($canonicalKey);
                        if (isset($byKey[$k])) {
                            return $byKey[$k];
                        }
                        if (isset($byName[strtolower($canonicalKey)])) {
                            return $byName[strtolower($canonicalKey)];
                        }
                    }
                }
            }
        }

        return $defaultId;
    }

}
