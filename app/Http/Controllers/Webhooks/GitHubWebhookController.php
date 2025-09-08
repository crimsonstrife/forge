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

final class GitHubWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $raw = $request->getContent();
        $headers = $request->headers->all();

        $delivery = WebhookDelivery::query()->create([
            'provider' => 'github',
            'event_type' => $request->header('X-GitHub-Event'),
            'signature' => $request->header('X-Hub-Signature-256'),
            'headers' => $headers,
            'payload' => $raw,
        ]);

        $factory = app('repo-provider-factory');
        /** @var RepositoryProviderInterface $provider */
        $provider = $factory('github');

        $repo = $provider->resolveRepositoryFromWebhook($request->headers->all(), $raw);
        if ($repo) { $delivery->update(['repository_id' => $repo->id]); }

        $event = $provider->normalizeWebhook($request->headers->all(), $raw);
        if (!$event || !$repo) {
            $delivery->update(['http_status' => 202]);
            return response()->noContent(202);
        }

        // Upsert the local issue based on external id/number
        $ref = IssueExternalRef::query()
            ->where('repository_id', $repo->id)
            ->where('external_issue_id', $event['external_issue_id'])
            ->first();

        if (!$ref) {
            // ignore events for issues not imported yet (initial import owns creation)
            $delivery->update(['http_status' => 202]);
            return response()->noContent(202);
        }

        $issue = Issue::query()->find($ref->issue_id);
        if ($issue) {
            // Update local fields you consider source-of-truth from remote:
            $issue->fill([
                'summary' => $event['title'] ?? $issue->summary,
                'description' => $event['body'] ?? $issue->description,
            ])->save();

            // Status mapping
            $mapped = $repo->statusMappings()->where('external_state', $event['state'])->first();
            if ($mapped) {
                $issue->issue_status_id = $mapped->issue_status_id;
                $issue->save();
            }

            // Assignee, Reporter conditional mapping
            // (same logic as in the import job; omitted here for brevity)

            $ref->update([
                'state' => $event['state'] ?? $ref->state,
                'payload' => $event['raw'] ?? $ref->payload,
            ]);
        }

        $delivery->update(['http_status' => 204]);
        return response()->noContent();
    }
}
