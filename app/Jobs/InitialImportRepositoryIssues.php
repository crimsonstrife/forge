<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\ProjectRepository;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

final class InitialImportRepositoryIssues implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public string $projectRepositoryId) {}

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

            // If nothing returned (e.g., a repo with only closed issues but something went wrong), surface that.
            if (empty($issues)) {
                $link->update([
                    'initial_import_finished_at' => now(),
                    'last_sync_status'           => 'ok',
                    'last_sync_error'            => 'Provider returned 0 issues. If the repo has only closed issues, ensure the token has access and the provider fetches closed items.',
                ]);
                return;
            }

            $statusMap = $repo->statusMappings->keyBy(fn ($m) => strtolower($m->external_state));

            // Lookups
            [$defaultTypeId, $typeByKey, $typeByName, $typeByTier] = $this->prepareIssueTypeLookups();
            [$defaultPriorityId, $prioByKey, $prioByName] = $this->preparePriorityLookups();

            foreach ($issues as $i) {
                $state = strtolower($i['state'] ?? '');

                // --- Resolve Issue Status
                $statusId = optional($statusMap->get($state))->issue_status_id;
                if (!$statusId) {
                    $statusId = IssueStatus::query()
                        ->when($state === 'closed', fn ($q) => $q->where('is_done', true),
                            fn ($q) => $q->where('is_done', false))
                        ->orderBy('order')
                        ->value('id');
                }

                // --- Resolve Type & Priority
                $issueTypeId = $this->inferIssueTypeIdFromLabels(
                    $i['labels'] ?? [], $typeByKey, $typeByName, $typeByTier, $defaultTypeId
                );

                $issuePriorityId = $this->inferPriorityIdFromLabels(
                    $i['labels'] ?? [], $prioByKey, $prioByName, $defaultPriorityId
                );

                // --- Idempotent upsert: find existing external ref first
                $externalIssueId = (string)($i['external_issue_id'] ?? '');
                $number          = (int)($i['number'] ?? 0);

                $ext = IssueExternalRef::query()
                    ->where('repository_id', $repo->id)
                    ->when($externalIssueId !== '', fn ($q) => $q->where('external_issue_id', $externalIssueId))
                    ->when($externalIssueId === '' && $number > 0, fn ($q) => $q->where('number', $number))
                    ->first();

                if ($ext) {
                    // Update the existing Issue + external ref (no duplicates)
                    $issue = Issue::query()->find($ext->issue_id);
                    if ($issue) {
                        $issue->fill([
                            'summary'           => $i['title'],
                            'description'       => $i['body'] ?? null,
                            'issue_status_id'   => $statusId,
                            'issue_type_id'     => $issueTypeId,
                            'issue_priority_id' => $issuePriorityId,
                            'updated_at'        => $i['updated_at'] ?? now(),
                            'closed_at'         => $i['closed_at'] ?? null,
                        ])->save();
                    }

                    $ext->fill([
                        'state'   => $i['state'] ?? null,
                        'url'     => $i['url'] ?? null,
                        'payload' => $i['raw'] ?? null,
                        'number'  => $number ?: $ext->number,
                    ])->save();
                } else {
                    // Create new Issue + external ref
                    $issue = Issue::query()->create([
                        'project_id'        => $project->id,
                        'summary'           => $i['title'],
                        'description'       => $i['body'] ?? null,
                        'issue_status_id'   => $statusId,
                        'issue_type_id'     => $issueTypeId,
                        'issue_priority_id' => $issuePriorityId,
                        'created_at'        => $i['created_at'] ?? now(),
                        'updated_at'        => $i['updated_at'] ?? now(),
                        'closed_at'         => $i['closed_at'] ?? null,
                    ]);

                    IssueExternalRef::query()->create([
                        'issue_id'          => $issue->id,
                        'repository_id'     => $repo->id,
                        'provider'          => $repo->provider,
                        'external_issue_id' => $externalIssueId,
                        'number'            => $number,
                        'url'               => $i['url'] ?? null,
                        'state'             => $i['state'] ?? null,
                        'payload'           => $i['raw'] ?? null,
                    ]);
                }

                // --- People mapping (assoc only if account connected)
                if (!empty($i['assignee_login'])) {
                    /** @var User|null $assignee */
                    $assignee = User::query()
                        ->whereHas('socialAccounts', function ($q) use ($repo, $i) {
                            $q->where('provider', $repo->provider)
                                ->where('nickname', $i['assignee_login']);
                        })->first();

                    if (isset($issue) && $assignee) {
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

                    if (isset($issue) && $reporter) {
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

    /** @return array{0:string,1:array<string,string>,2:array<string,string>,3:array<string,string>} */
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
            if ($t->key)  { $byKey[strtolower((string) $t->key)]   = (string) $t->id; }
            if ($t->name) { $byName[strtolower((string) $t->name)] = (string) $t->id; }
            if ($t->tier) { $byTier[strtolower($t->tier->value)]   = (string) $t->id; }
        }

        return [(string) $default->id, $byKey, $byName, $byTier];
    }

    /** @return array{0:string,1:array<string,string>,2:array<string,string>} */
    private function preparePriorityLookups(): array
    {
        $priorities = IssuePriority::query()->get(['id', 'key', 'name']);

        $default = $priorities->firstWhere('key', 'MEDIUM') ?? $priorities->first();

        if (!$default) {
            throw new \RuntimeException('No IssuePriorities exist; seed IssuePriorities before importing.');
        }

        $byKey  = [];
        $byName = [];
        foreach ($priorities as $p) {
            if ($p->key)  { $byKey[strtolower((string) $p->key)]   = (string) $p->id; }
            if ($p->name) { $byName[strtolower((string) $p->name)] = (string) $p->id; }
        }

        return [(string) $default->id, $byKey, $byName];
    }

    /** @param array<int,string> $labels */
    private function inferPriorityIdFromLabels(array $labels, array $byKey, array $byName, string $defaultId): string
    {
        $synonyms = [
            'HIGHEST' => ['p0', 'blocker', 'critical', 'urgent', 'sev1', 'highest'],
            'HIGH'    => ['p1', 'high', 'important', 'sev2'],
            'MEDIUM'  => ['p2', 'medium', 'normal', 'default'],
            'LOW'     => ['p3', 'low', 'minor', 'sev4'],
            'LOWEST'  => ['p4', 'lowest', 'trivial'],
        ];

        foreach ($labels as $label) {
            $l = strtolower((string) $label);

            if (isset($byKey[$l]))  { return $byKey[$l]; }
            if (isset($byName[$l])) { return $byName[$l]; }

            if (preg_match('/^p([0-4])$/i', $l, $m)) {
                $map = ['0' => 'HIGHEST', '1' => 'HIGH', '2' => 'MEDIUM', '3' => 'LOW', '4' => 'LOWEST'];
                $k = strtolower($map[$m[1]]);
                if (isset($byKey[$k])) { return $byKey[$k]; }
            }

            foreach ($synonyms as $canonicalKey => $alts) {
                foreach ($alts as $alt) {
                    if ($l === strtolower($alt)) {
                        $k = strtolower($canonicalKey);
                        if (isset($byKey[$k])) { return $byKey[$k]; }
                        if (isset($byName[strtolower($canonicalKey)])) { return $byName[strtolower($canonicalKey)]; }
                    }
                }
            }
        }

        return $defaultId;
    }

    /** @param array<int,string> $labels */
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

            if (isset($byKey[$l])) { return $byKey[$l]; }
            if (isset($byName[$l])) { return $byName[$l]; }

            foreach ($synonyms as $canonical => $alts) {
                if ($l === $canonical || in_array($l, $alts, true)) {
                    $canonKey = strtolower($canonical);
                    if (isset($byKey[$canonKey])) { return $byKey[$canonKey]; }
                    if (isset($byTier[$canonKey])) { return $byTier[$canonKey]; }
                }
            }
        }

        return $defaultId;
    }
}
