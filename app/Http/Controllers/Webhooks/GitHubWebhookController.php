<?php
namespace App\Http\Controllers\Webhooks;

use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\Repository;
use App\Models\WebhookDelivery;
use App\Contracts\RepositoryProviderInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Throwable;

final class GitHubWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $raw     = $request->getContent();
        $headers = $request->headers->all();

        // Optional: verify HMAC if you set a secret in services.github.webhook_secret
        $secret = (string) config('services.github.webhook_secret', '');
        if ($secret !== '') {
            $sig     = (string) $request->header('X-Hub-Signature-256', '');
            $calc    = 'sha256=' . hash_hmac('sha256', $raw, $secret);
            if (!hash_equals($calc, $sig)) {
                WebhookDelivery::query()->create([
                    'provider'    => 'github',
                    'event_type'  => $request->header('X-GitHub-Event'),
                    'signature'   => $sig,
                    'headers'     => $headers,
                    'payload'     => $raw,
                    'http_status' => 401,
                    'processing_error' => 'Invalid signature',
                ]);
                return response()->noContent(401);
            }
        }

        $delivery = WebhookDelivery::query()->create([
            'provider'   => 'github',
            'event_type' => $request->header('X-GitHub-Event'),
            'signature'  => $request->header('X-Hub-Signature-256'),
            'headers'    => $headers,
            'payload'    => $raw,
        ]);

        try {
            $factory = app('repo-provider-factory');
            /** @var RepositoryProviderInterface $provider */
            $provider = $factory('github');

            $repo = $provider->resolveRepositoryFromWebhook($headers, $raw);
            if ($repo) {
                $delivery->update(['repository_id' => $repo->id]);
            }

            $event = $provider->normalizeWebhook($headers, $raw);
            if (!$event || !$repo) {
                $delivery->update(['http_status' => 202]);
                return response()->noContent(202);
            }

            // Try finding the external ref by external_issue_id OR number
            $externalId = (string) ($event['external_issue_id'] ?? '');
            $number     = (int) ($event['number'] ?? 0);

            $refQuery = IssueExternalRef::query()
                ->where('repository_id', $repo->id);

            if ($externalId !== '') {
                $refQuery->where('external_issue_id', $externalId);
            } elseif ($number > 0) {
                $refQuery->where('number', $number);
            }

            /** @var IssueExternalRef|null $ref */
            $ref = $refQuery->first();

            if (!$ref) {
                // Ignore events for issues not imported yet (initial import owns creation)
                $delivery->update(['http_status' => 202]);
                return response()->noContent(202);
            }

            /** @var Issue|null $issue */
            $issue = Issue::query()->find($ref->issue_id);
            if ($issue) {
                // Status mapping (case-insensitive)
                $state    = strtolower((string) ($event['state'] ?? 'open'));
                $mapped   = $repo->statusMappings()
                    ->whereRaw('LOWER(external_state) = ?', [$state])
                    ->first();

                if ($mapped) {
                    $issue->issue_status_id = $mapped->issue_status_id;
                } else {
                    // Fallback: choose first non-done for open, any done for closed
                    $issue->issue_status_id = \App\Models\IssueStatus::query()
                        ->when($state === 'closed', fn ($q) => $q->where('is_done', true),
                            fn ($q) => $q->where('is_done', false))
                        ->orderBy('order')
                        ->value('id') ?? $issue->issue_status_id;
                }

                // Basic field updates
                $issue->summary     = $event['title'] ?? $issue->summary;
                $issue->description = $event['body']  ?? $issue->description;

                // closed_at handling
                if ($state === 'closed') {
                    $issue->closed_at = $event['closed_at'] ?? $issue->closed_at ?? now();
                } elseif ($state === 'open') {
                    // If reopened, clear closed_at
                    if (!empty($issue->closed_at)) {
                        $issue->closed_at = null;
                    }
                }

                $issue->save();
            }

            // Persist the latest external snapshot
            $ref->update([
                'state'   => $event['state'] ?? $ref->state,
                'payload' => $event['raw']   ?? $ref->payload,
                'url'     => $event['url']   ?? ($ref->url ?? null),
                'number'  => $number > 0 ? $number : $ref->number,
            ]);

            $delivery->update(['http_status' => 204]);
            return response()->noContent();
        } catch (Throwable $e) {
            $delivery->update([
                'http_status'       => 500,
                'processing_error'  => mb_strimwidth($e->getMessage(), 0, 8000),
            ]);
            report($e);
            return response()->noContent(500);
        }
    }
}
