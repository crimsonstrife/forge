<?php

namespace Tests\Feature\Api\V1\Feedback;

use App\Mail\FeedbackMagicLinkMail;
use App\Models\FeedbackBoard;
use App\Models\FeedbackComment;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackMagicLink;
use App\Models\FeedbackPost;
use App\Models\FeedbackPostVote;
use App\Models\FeedbackSession;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\User;
use App\Services\Feedback\FeedbackKeyService;
use App\Services\Support\IngestKeyManager;
use Database\Seeders\IssueEnumsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class FeedbackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_magic_link_request_never_reveals_whether_email_exists(): void
    {
        Mail::fake();
        [$board, $ingestToken] = $this->boardWithIngestToken();
        FeedbackIdentity::factory()->create([
            'email_encrypted' => 'known@example.test',
            'email_hash' => FeedbackIdentity::emailHash('known@example.test'),
        ]);

        $first = $this->postJson('/api/v1/feedback/auth/request', [
            'email' => 'known@example.test',
            'board_slug' => $board->slug,
        ], ['X-Ingest-Key' => $ingestToken]);

        $second = $this->postJson('/api/v1/feedback/auth/request', [
            'email' => 'new@example.test',
            'board_slug' => $board->slug,
        ], ['X-Ingest-Key' => $ingestToken]);

        $first->assertAccepted();
        $second->assertAccepted();
        $this->assertSame($first->getContent(), $second->getContent());
        Mail::assertQueued(FeedbackMagicLinkMail::class, 2);
    }

    public function test_magic_link_verify_requires_display_name_then_issues_session(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->unverified()->create(['display_name' => null]);
        $token = 'valid-token';
        FeedbackMagicLink::factory()->create([
            'identity_id' => $identity->getKey(),
            'service_product_id' => $board->service_product_id,
            'email_hash' => $identity->email_hash,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->postJson('/api/v1/feedback/auth/verify', ['token' => $token], ['X-Ingest-Key' => $ingestToken])
            ->assertStatus(409)
            ->assertJson(['requires_display_name' => true]);

        $this->postJson('/api/v1/feedback/auth/verify', [
            'token' => $token,
            'display_name' => 'Alex',
        ], ['X-Ingest-Key' => $ingestToken])
            ->assertOk()
            ->assertJsonStructure(['session_token', 'expires_at', 'identity' => ['id', 'display_name']])
            ->assertJsonPath('identity.display_name', 'Alex');
    }

    public function test_magic_link_verify_rejects_bad_consumed_and_expired_tokens(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->create();

        FeedbackMagicLink::factory()->create([
            'identity_id' => $identity->getKey(),
            'service_product_id' => $board->service_product_id,
            'token_hash' => hash('sha256', 'consumed'),
            'consumed_at' => now(),
        ]);
        FeedbackMagicLink::factory()->create([
            'identity_id' => $identity->getKey(),
            'service_product_id' => $board->service_product_id,
            'token_hash' => hash('sha256', 'expired'),
            'expires_at' => now()->subMinute(),
        ]);

        foreach (['bad', 'consumed', 'expired'] as $token) {
            $this->postJson('/api/v1/feedback/auth/verify', ['token' => $token], ['X-Ingest-Key' => $ingestToken])
                ->assertUnprocessable();
        }
    }

    public function test_authenticated_post_creation_requires_verified_email_and_is_rate_limited(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $unverified = FeedbackIdentity::factory()->unverified()->create();

        $this->postJson('/api/v1/feedback/boards/'.$board->slug.'/posts', [
            'title' => 'Unverified idea',
            'body' => 'Please add this.',
        ], $this->feedbackHeaders($ingestToken, $this->sessionToken($unverified)))
            ->assertForbidden();

        $identity = FeedbackIdentity::factory()->create();
        $token = $this->sessionToken($identity);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/feedback/boards/'.$board->slug.'/posts', [
                'title' => 'Idea '.$i,
                'body' => 'Please add this.',
            ], $this->feedbackHeaders($ingestToken, $token))->assertSuccessful();
        }

        $this->postJson('/api/v1/feedback/boards/'.$board->slug.'/posts', [
            'title' => 'One too many',
            'body' => 'Please add this.',
        ], $this->feedbackHeaders($ingestToken, $token))->assertTooManyRequests();
    }

    public function test_voting_toggles_and_recomputes_net_score(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->create();
        $post = FeedbackPost::factory()->create(['board_id' => $board->getKey(), 'status_id' => $board->defaultStatus()?->getKey()]);
        $headers = $this->feedbackHeaders($ingestToken, $this->sessionToken($identity));

        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/vote', ['value' => 1], $headers)->assertOk();
        $this->assertSame(1, $post->fresh()->net_score);

        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/vote', ['value' => 1], $headers)->assertOk();
        $this->assertSame(1, $post->fresh()->net_score);

        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/vote', ['value' => -1], $headers)->assertOk();
        $this->assertSame(-1, $post->fresh()->net_score);

        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/vote', ['value' => 0], $headers)->assertOk();
        $this->assertSame(0, $post->fresh()->net_score);
        $this->assertDatabaseMissing('feedback_post_votes', ['post_id' => $post->getKey(), 'identity_id' => $identity->getKey()]);
    }

    public function test_list_endpoint_includes_my_vote_when_session_is_present(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->create();
        $post = FeedbackPost::factory()->create(['board_id' => $board->getKey(), 'status_id' => $board->defaultStatus()?->getKey()]);
        FeedbackPostVote::factory()->create(['post_id' => $post->getKey(), 'identity_id' => $identity->getKey(), 'value' => 1]);

        $this->getJson('/api/v1/feedback/boards/'.$board->slug.'/posts', ['X-Ingest-Key' => $ingestToken])
            ->assertOk()
            ->assertJsonPath('data.0.my_vote', null);

        $this->getJson('/api/v1/feedback/boards/'.$board->slug.'/posts', $this->feedbackHeaders($ingestToken, $this->sessionToken($identity)))
            ->assertOk()
            ->assertJsonPath('data.0.my_vote', 1);
    }

    public function test_board_lookup_reports_when_slug_does_not_belong_to_ingest_key_product(): void
    {
        $firstProduct = ServiceProduct::factory()->create(['key' => 'OMP']);
        FeedbackBoard::factory()->create(['service_product_id' => $firstProduct->getKey(), 'slug' => 'ideas']);

        $secondProduct = ServiceProduct::factory()->create(['key' => 'ALT']);
        $ingestToken = app(IngestKeyManager::class)->generate($secondProduct, 'Other product key')['token'];

        $this->getJson('/api/v1/feedback/boards/ideas', ['X-Ingest-Key' => $ingestToken])
            ->assertNotFound()
            ->assertJsonPath('message', "Feedback board [ideas] was not found for this ingest key's service product.");
    }

    public function test_admin_convert_to_issue_creates_issue_link_and_optionally_moves_status(): void
    {
        $this->seed(IssueEnumsSeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        [$board] = $this->boardWithIngestToken();
        $post = FeedbackPost::factory()->create(['board_id' => $board->getKey(), 'status_id' => $board->defaultStatus()?->getKey()]);
        $project = Project::factory()->create();

        $this->postJson('/api/v1/feedback/admin/posts/'.$post->id.'/convert-issue', [
            'project_id' => $project->getKey(),
            'mark_planned' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('issues', ['project_id' => $project->getKey(), 'summary' => $post->title]);
        $issue = Issue::query()->where('summary', $post->title)->firstOrFail();
        $this->assertDatabaseHas('feedback_post_issue_links', ['post_id' => $post->getKey(), 'issue_id' => $issue->getKey()]);
        $this->assertSame('planned', $post->fresh()->status->slug);
    }

    public function test_blocked_identity_cannot_post_comment_or_vote(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->blocked()->create();
        $post = FeedbackPost::factory()->create(['board_id' => $board->getKey(), 'status_id' => $board->defaultStatus()?->getKey()]);
        $headers = $this->feedbackHeaders($ingestToken, $this->sessionToken($identity));

        $this->postJson('/api/v1/feedback/boards/'.$board->slug.'/posts', ['title' => 'Nope', 'body' => 'Blocked'], $headers)->assertForbidden();
        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/comments', ['body' => 'Blocked'], $headers)->assertForbidden();
        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/vote', ['value' => 1], $headers)->assertForbidden();
    }

    public function test_comment_threading_rejects_two_level_nesting(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->create();
        $post = FeedbackPost::factory()->create(['board_id' => $board->getKey(), 'status_id' => $board->defaultStatus()?->getKey()]);
        $parent = FeedbackComment::factory()->create(['post_id' => $post->getKey()]);
        $reply = FeedbackComment::factory()->create(['post_id' => $post->getKey(), 'parent_comment_id' => $parent->getKey()]);

        $this->postJson('/api/v1/feedback/posts/'.$post->id.'/comments', [
            'body' => 'Too deep',
            'parent_comment_id' => $reply->getKey(),
        ], $this->feedbackHeaders($ingestToken, $this->sessionToken($identity)))->assertUnprocessable();
    }

    public function test_xss_payloads_are_stripped_from_markdown_render(): void
    {
        [$board, $ingestToken] = $this->boardWithIngestToken();
        $identity = FeedbackIdentity::factory()->create();

        $response = $this->postJson('/api/v1/feedback/boards/'.$board->slug.'/posts', [
            'title' => 'Security check',
            'body' => '<script>alert(1)</script>[bad](javascript:alert(1))',
        ], $this->feedbackHeaders($ingestToken, $this->sessionToken($identity)));

        $response->assertSuccessful();
        $html = FeedbackPost::query()->firstOrFail()->body_html;
        $this->assertStringNotContainsString('<script', strtolower($html));
        $this->assertStringNotContainsString('javascript:', strtolower($html));
    }

    public function test_feedback_keys_are_unique_across_boards_with_the_same_product_prefix(): void
    {
        $product = ServiceProduct::factory()->create(['key' => 'OMP']);
        $firstBoard = FeedbackBoard::factory()->create(['service_product_id' => $product->getKey(), 'slug' => 'ideas']);
        $secondBoard = FeedbackBoard::factory()->create(['service_product_id' => $product->getKey(), 'slug' => 'bugs']);

        $keys = [
            app(FeedbackKeyService::class)->nextKey($firstBoard),
            app(FeedbackKeyService::class)->nextKey($secondBoard),
            app(FeedbackKeyService::class)->nextKey($firstBoard),
        ];

        $this->assertSame(['OMP-F-1', 'OMP-F-2', 'OMP-F-3'], $keys);
    }

    /** @return array{0:FeedbackBoard,1:string} */
    private function boardWithIngestToken(): array
    {
        $product = ServiceProduct::factory()->create(['key' => 'OMP']);
        $board = FeedbackBoard::factory()->create(['service_product_id' => $product->getKey(), 'slug' => 'ideas']);
        $token = app(IngestKeyManager::class)->generate($product, 'Test key')['token'];

        return [$board, $token];
    }

    private function sessionToken(FeedbackIdentity $identity): string
    {
        $token = 'fb_sess_'.Str::random(43);
        FeedbackSession::factory()->create([
            'identity_id' => $identity->getKey(),
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(30),
        ]);

        return $token;
    }

    /** @return array<string,string> */
    private function feedbackHeaders(string $ingestToken, string $sessionToken): array
    {
        return [
            'X-Ingest-Key' => $ingestToken,
            'Authorization' => 'Bearer '.$sessionToken,
        ];
    }
}
