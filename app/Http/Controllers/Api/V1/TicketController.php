<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\SupportIdentity;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Services\Support\TextRedactor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

final class TicketController extends Controller
{
    public function __construct(private TextRedactor $redactor)
    {
    }

    /**
     * @throws Throwable
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        /** @var array<string,mixed> $payload */
        $payload = $request->validated();

        // Map severity -> priority (fallback gracefully)
        $priority = $this->mapSeverityToPriorityId($payload['severity'] ?? null);

        // Prefer a "Bug" type if it exists, else first type:
        $typeId = TicketType::query()->where('name', 'Bug')->value('id')
            ?? TicketType::query()->orderBy('name')->value('id');

        // Pick a default "New/Open" status if defined, else first:
        $statusId = TicketStatus::query()->whereIn('name', ['New', 'Open'])->value('id')
            ?? TicketStatus::query()->orderBy('name')->value('id');

        // Build or match the support identity
        $identity = $this->resolveIdentity(
            email: $payload['email'] ?? null,
            playerId: $payload['player_id'] ?? null,
        );

        // Redact PII from description
        $cleanDescription = $this->redactor->redact($payload['description']);

        $ingestKey = $request->attributes->get('ingest_key'); // ServiceProductIngestKey|null
        $serviceProductId = $ingestKey?->service_product_id ?? $payload['service_product_id'];

        /** @var Ticket $ticket */
        $ticket = DB::transaction(static function () use ($payload, $identity, $priority, $typeId, $statusId, $cleanDescription, $serviceProductId) {
            $t = Ticket::query()->create([
                'summary'            => $payload['summary'],
                'description'        => $cleanDescription,
                'service_product_id' => $serviceProductId,
                'priority_id'        => $priority,
                'type_id'            => $typeId,
                'status_id'          => $statusId,
                'support_identity_id' => $identity?->getKey(),
                'source'             => 'api',                // helpful flag
                'channel'            => 'game',               // optional
                'build'              => $payload['build'] ?? null,
                'platform'           => $payload['platform'] ?? null,
                'meta'               => $payload['metadata'] ?? [], // cast json on model
            ]);

            /** @var array<int,UploadedFile> $files */
            // TODO: Implement attachment handling if required in the future.
            return $t->load([
                'status:id,name',
                'priority:id,name',
                'type:id,name',
                'serviceProduct:id,name',
            ]);
        });

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    private function resolveIdentity(?string $email, ?string $playerId): ?SupportIdentity
    {
        if ($email) {
            /** @var SupportIdentity|null $identity */
            $identity = SupportIdentity::query()->where('email', $email)->first();
            if ($identity !== null) {
                // backfill player id
                if ($playerId && empty($identity->external_user_id)) {
                    $identity->external_user_id = $playerId;
                    $identity->save();
                }
                return $identity;
            }

            return SupportIdentity::query()->create([
                'email'             => $email,
                'external_user_id'  => $playerId,
                'display_name'      => $email, // or null
                'source'            => 'api',
            ]);
        }

        if ($playerId) {
            return SupportIdentity::query()->firstOrCreate(
                ['external_user_id' => $playerId],
                ['source' => 'api']
            );
        }

        return null; // completely anonymous
    }

    private function mapSeverityToPriorityId(?string $severity): ?string
    {
        if ($severity === null) {
            return TicketPriority::query()->where('name', 'Medium')->value('id')
                ?? TicketPriority::query()->orderBy('name')->value('id');
        }

        $map = [
            'critical' => ['Critical', 'P0'],
            'major'    => ['High', 'P1'],
            'minor'    => ['Medium', 'P2'],
            'trivial'  => ['Low', 'P3'],
        ];

        $candidates = $map[strtolower($severity)] ?? ['Medium'];
        return TicketPriority::query()->whereIn('name', $candidates)->value('id')
            ?? TicketPriority::query()->orderBy('name')->value('id');
    }
}
