<?php

namespace Tests\Feature\Integrations\Sentry;

use App\Integrations\Sentry\Jobs\SyncCommentToSentryJob;
use App\Integrations\Sentry\Jobs\SyncIssueStatusToSentryJob;
use App\Integrations\Sentry\Services\SentryClient;
use App\Integrations\Sentry\Support\SentrySyncContext;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use App\Settings\SentrySettings;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OutboundSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // RefreshDatabase wraps each test in a transaction that never commits.
        // ->afterCommit() jobs would never dispatch. Swap the txn manager on
        // the active connection so callbacks fire immediately.
        $immediate = new class () extends DatabaseTransactionsManager {
            public function addCallback($callback)
            {
                $callback();

                return null;
            }
        };
        $this->app->instance('db.transactions', $immediate);
        DB::connection()->setTransactionManager($immediate);

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);

        $settings = app(SentrySettings::class);
        $settings->enabled = true;
        $settings->org_slug = 'acme';
        $settings->client_id = 'cid';
        $settings->client_secret = 'cs';
        $settings->auth_token = 'tok';
        $settings->api_base = 'https://sentry.io/api/0';
        $settings->save();
    }

    public function test_status_transition_to_done_dispatches_resolve_job(): void
    {
        Bus::fake();

        $issue = $this->makeLinkedIssue();
        $done = IssueStatus::query()->where('is_done', true)->orderBy('order')->first();

        $issue->issue_status_id = $done->id;
        $issue->save();

        Bus::assertDispatched(SyncIssueStatusToSentryJob::class, function (SyncIssueStatusToSentryJob $job) use ($issue) {
            return $job->issueId === $issue->id && $job->resolve === true;
        });
    }

    public function test_status_transition_off_done_dispatches_unresolve_job(): void
    {
        Bus::fake([SyncIssueStatusToSentryJob::class]);

        $issue = $this->makeLinkedIssue();
        $done = IssueStatus::query()->where('is_done', true)->orderBy('order')->first();
        $open = IssueStatus::query()->where('is_done', false)->orderBy('order')->first();

        $issue->issue_status_id = $done->id;
        $issue->save();
        Bus::assertDispatched(SyncIssueStatusToSentryJob::class, fn ($j) => $j->resolve === true);

        $issue->issue_status_id = $open->id;
        $issue->save();
        Bus::assertDispatched(SyncIssueStatusToSentryJob::class, fn ($j) => $j->resolve === false);
    }

    public function test_no_job_dispatched_when_issue_not_linked_to_sentry(): void
    {
        Bus::fake([SyncIssueStatusToSentryJob::class]);

        $issue = $this->makeIssue();
        $done = IssueStatus::query()->where('is_done', true)->orderBy('order')->first();

        $issue->issue_status_id = $done->id;
        $issue->save();

        Bus::assertNotDispatched(SyncIssueStatusToSentryJob::class);
    }

    public function test_no_job_dispatched_when_sentry_sync_context_active(): void
    {
        Bus::fake([SyncIssueStatusToSentryJob::class]);

        $issue = $this->makeLinkedIssue();
        $done = IssueStatus::query()->where('is_done', true)->orderBy('order')->first();

        SentrySyncContext::run(function () use ($issue, $done) {
            $issue->issue_status_id = $done->id;
            $issue->save();
        });

        Bus::assertNotDispatched(SyncIssueStatusToSentryJob::class);
    }

    public function test_comment_on_linked_issue_dispatches_comment_job(): void
    {
        Bus::fake([SyncCommentToSentryJob::class]);

        $issue = $this->makeLinkedIssue();
        $user = User::factory()->create();

        $comment = new Comment([
            'body' => 'looking at it',
            'user_id' => $user->id,
        ]);
        $comment->commentable_type = Issue::class;
        $comment->commentable_id = $issue->id;
        $comment->save();

        Bus::assertDispatched(SyncCommentToSentryJob::class, function (SyncCommentToSentryJob $j) use ($comment) {
            return $j->commentId === $comment->id;
        });
    }

    public function test_sentry_client_calls_resolve_endpoint(): void
    {
        Http::fake([
            'sentry.io/api/0/issues/12345/' => Http::response('', 200),
            'sentry.io/api/0/issues/12345/comments/' => Http::response('', 201),
        ]);

        app(SentryClient::class)->resolveIssue('12345');
        app(SentryClient::class)->postComment('12345', 'hello');

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT'
                && str_ends_with($request->url(), '/issues/12345/')
                && $request['status'] === 'resolved';
        });
        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_ends_with($request->url(), '/issues/12345/comments/')
                && $request['text'] === 'hello';
        });
    }

    public function test_sentry_client_creates_issue_alert_rule_for_sentry_app_action(): void
    {
        $project = Project::factory()->create();
        $type = IssueType::query()->where('key', 'BUG')->firstOrFail();
        $priority = IssuePriority::query()->where('key', 'HIGH')->firstOrFail();

        $settings = app(SentrySettings::class);
        $settings->installation_uuid = '9a89a822-6e0b-4b62-9b99-905b9d742dd1';
        $settings->save();

        Http::fake([
            'sentry.io/api/0/projects/acme/crash-game/rules/' => Http::response(['id' => 'rule-123'], 201),
        ]);

        $rule = app(SentryClient::class)->createIssueAlertRule(
            sentryProjectSlug: 'crash-game',
            ruleName: 'Send new issues to Forge',
            forgeProjectId: (string) $project->id,
            issueTypeId: $type->id,
            priorityId: $priority->id,
            frequency: 5,
        );

        $this->assertSame('rule-123', $rule['id']);

        Http::assertSent(function ($request) use ($project, $type, $priority) {
            $action = $request['actions'][0] ?? [];

            return $request->method() === 'POST'
                && str_ends_with($request->url(), '/projects/acme/crash-game/rules/')
                && $request['conditions'][0]['id'] === 'sentry.rules.conditions.first_seen_event.FirstSeenEventCondition'
                && $action['id'] === 'sentry.rules.actions.notify_event_sentry_app.NotifyEventSentryAppAction'
                && $action['sentryAppInstallationUuid'] === '9a89a822-6e0b-4b62-9b99-905b9d742dd1'
                && ! array_key_exists('hasSchemaFormConfig', $action)
                && $action['settings'] === [
                    ['name' => 'forge_project_id', 'value' => (string) $project->id],
                    ['name' => 'forge_issue_type_id', 'value' => (string) $type->id],
                    ['name' => 'forge_priority_id', 'value' => (string) $priority->id],
                ];
        });
    }

    private function makeIssue(): Issue
    {
        $project = Project::factory()->create();
        $type = IssueType::query()->where('key', 'TASK')->first();
        $prio = IssuePriority::query()->where('key', 'MEDIUM')->first();
        $open = IssueStatus::query()->where('is_done', false)->orderBy('order')->first();

        return Issue::create([
            'project_id' => $project->id,
            'issue_type_id' => $type->id,
            'issue_priority_id' => $prio->id,
            'issue_status_id' => $open->id,
            'reporter_id' => User::factory()->create()->id,
            'summary' => 'demo',
            'description' => 'demo',
        ]);
    }

    private function makeLinkedIssue(): Issue
    {
        $issue = $this->makeIssue();

        IssueExternalRef::create([
            'issue_id' => $issue->id,
            'provider' => 'sentry',
            'external_issue_id' => '12345',
            'state' => 'open',
            'url' => 'https://sentry.io/issues/12345/',
        ]);

        return $issue;
    }
}
