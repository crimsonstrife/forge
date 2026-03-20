<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueNotificationPreference;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use App\Notifications\IssueActivityNotification;
use App\Notifications\IssueDailyDigest;
use App\Notifications\MentionNotification;
use App\Services\Issues\IssueCollaborationService;
use App\Services\Issues\IssueCommentService;
use Carbon\CarbonImmutable;
use Database\Seeders\IssueEnumsSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class IssueCollaborationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(IssueEnumsSeeder::class);
    }

    public function test_comment_notifications_skip_users_who_are_already_mentioned(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $follower = User::factory()->create();
        $mentioned = User::factory()->create();
        $issue = $this->makeIssue($author);

        app(IssueCollaborationService::class)->follow($issue, $follower);

        $body = sprintf(
            '<p>Need a second set of eyes <a href="#user" data-mention-type="user" data-mention-id="%s">@%s</a>.</p>',
            $mentioned->getKey(),
            e($mentioned->name),
        );

        app(IssueCommentService::class)->create($issue, $author, $body);

        Notification::assertSentTo($follower, IssueActivityNotification::class);
        Notification::assertSentTo($mentioned, MentionNotification::class);
        Notification::assertNotSentTo($mentioned, IssueActivityNotification::class);

        $this->assertDatabaseHas('mentions', [
            'model_type' => Comment::class,
            'recipient_type' => User::class,
            'recipient_id' => $mentioned->getKey(),
        ]);

        $this->assertDatabaseHas('issue_followers', [
            'issue_id' => $issue->getKey(),
            'user_id' => $mentioned->getKey(),
        ]);
    }

    public function test_mentions_create_database_notifications_immediately(): void
    {
        $author = User::factory()->create();
        $mentioned = User::factory()->create();
        $issue = $this->makeIssue($author);

        $body = sprintf(
            '<p>Hello <a href="#user" data-mention-type="user" data-mention-id="%s">@%s</a></p>',
            $mentioned->getKey(),
            e($mentioned->name),
        );

        app(IssueCommentService::class)->create($issue, $author, $body);

        $this->assertDatabaseHas('notifications', [
            'type' => MentionNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $mentioned->getKey(),
        ]);
    }

    public function test_mentions_table_uses_non_integer_ids(): void
    {
        $columnType = Schema::getColumnType('mentions', 'model_id');

        $this->assertNotSame('integer', $columnType);
        $this->assertNotSame('bigint', $columnType);
    }

    public function test_status_change_notifications_respect_preferences(): void
    {
        Notification::fake();

        $actor = User::factory()->create();
        $follower = User::factory()->create();
        $issue = $this->makeIssue($actor);

        app(IssueCollaborationService::class)->follow($issue, $follower);
        IssueNotificationPreference::query()->create([
            'user_id' => $follower->getKey(),
            'notify_on_status_change' => false,
        ]);

        $nextStatusId = IssueStatus::query()
            ->where('id', '!=', $issue->issue_status_id)
            ->orderBy('id')
            ->value('id');

        $this->actingAs($actor);

        $issue->issue_status_id = $nextStatusId;
        $issue->save();

        Notification::assertNothingSentTo($follower);
    }

    public function test_daily_digest_command_sends_a_digest_to_opted_in_users(): void
    {
        $user = User::factory()->create();
        $issue = $this->makeIssue($user);

        IssueNotificationPreference::query()->create([
            'user_id' => $user->getKey(),
            'daily_digest_enabled' => true,
        ]);

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => IssueActivityNotification::class,
            'data' => [
                'event' => 'comment',
                'issue_id' => $issue->getKey(),
                'issue_key' => $issue->key,
                'project_id' => $issue->project_id,
                'title' => 'New comment on '.$issue->key,
                'summary' => 'A comment needs your attention.',
                'url' => route('issues.show', ['project' => $issue->project, 'issue' => $issue]),
            ],
        ]);

        Notification::fake();

        Artisan::call('issues:send-digests');

        Notification::assertSentTo($user, IssueDailyDigest::class);
    }

    public function test_daily_digest_command_only_advances_last_sent_at_after_a_digest_is_delivered(): void
    {
        $user = User::factory()->create();
        $issue = $this->makeIssue($user);
        $lastSentAt = CarbonImmutable::now()->subDay();
        $notificationCreatedAt = CarbonImmutable::now()->subHour();

        $preference = IssueNotificationPreference::query()->create([
            'user_id' => $user->getKey(),
            'daily_digest_enabled' => true,
            'daily_digest_last_sent_at' => $lastSentAt,
        ]);

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => MentionNotification::class,
            'data' => [
                'title' => 'Mentioned on '.$issue->key,
                'summary' => 'A comment mentioned you.',
                'url' => route('issues.show', ['project' => $issue->project, 'issue' => $issue]),
            ],
            'created_at' => $notificationCreatedAt,
            'updated_at' => $notificationCreatedAt,
        ]);

        Notification::fake();

        Artisan::call('issues:send-digests');

        $preference->refresh();

        Notification::assertSentTo($user, IssueDailyDigest::class);
        $this->assertSame(
            $notificationCreatedAt->toDateTimeString(),
            $preference->daily_digest_last_sent_at?->toDateTimeString(),
        );
    }

    public function test_daily_digest_command_does_not_advance_last_sent_at_when_no_digest_is_sent(): void
    {
        $user = User::factory()->create();
        $lastSentAt = CarbonImmutable::now()->subDay();

        $preference = IssueNotificationPreference::query()->create([
            'user_id' => $user->getKey(),
            'daily_digest_enabled' => true,
            'daily_digest_last_sent_at' => $lastSentAt,
        ]);

        Notification::fake();

        Artisan::call('issues:send-digests');

        $preference->refresh();

        Notification::assertNothingSent();
        $this->assertSame(
            $lastSentAt->toDateTimeString(),
            $preference->daily_digest_last_sent_at?->toDateTimeString(),
        );
    }

    public function test_legacy_comment_html_is_sanitized_when_rendered(): void
    {
        $author = User::factory()->create();
        $issue = $this->makeIssue($author);

        $comment = $issue->comments()->create([
            'user_id' => $author->getKey(),
            'body' => '<p>Hello</p><img src="x" onerror="alert(1)"><script>alert(1)</script>',
        ]);

        $this->assertStringContainsString('<p>Hello</p>', $comment->rendered_body);
        $this->assertStringNotContainsString('onerror=', $comment->rendered_body);
        $this->assertStringNotContainsString('<script>', $comment->rendered_body);
    }

    public function test_settings_profile_page_shows_issue_notification_preferences(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Issue Collaboration Notifications')
            ->assertSee('Notify me when someone mentions me in an issue comment');
    }

    private function makeIssue(User $reporter): Issue
    {
        $project = Project::factory()->create([
            'lead_id' => $reporter->getKey(),
        ]);

        $typeId = IssueType::query()->value('id');
        $statusId = IssueStatus::query()->value('id');
        $priorityId = IssuePriority::query()->value('id');

        return Issue::query()->create([
            'project_id' => $project->getKey(),
            'issue_type_id' => $typeId,
            'issue_status_id' => $statusId,
            'issue_priority_id' => $priorityId,
            'reporter_id' => $reporter->getKey(),
            'summary' => 'Collaboration test issue',
            'description' => '<p>Issue body</p>',
        ]);
    }
}
