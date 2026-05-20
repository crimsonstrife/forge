<?php

namespace App\Services\Feedback;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackSession;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackSessionService
{
    public const PREFIX = 'fb_sess_';

    /** @return array{token:string,expires_at:CarbonInterface} */
    public function issue(FeedbackIdentity $identity, Request $request): array
    {
        $token = self::PREFIX.Str::random(43);
        $expiresAt = now()->addDays(30);

        FeedbackSession::query()->create([
            'identity_id' => $identity->getKey(),
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt,
            'last_seen_at' => now(),
            'last_seen_ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return ['token' => $token, 'expires_at' => $expiresAt];
    }

    public function resolveBearer(string $bearer, ?Request $request = null): ?FeedbackIdentity
    {
        if (! str_starts_with($bearer, self::PREFIX)) {
            return null;
        }

        /** @var FeedbackSession|null $session */
        $session = FeedbackSession::query()
            ->with('identity')
            ->where('token_hash', hash('sha256', $bearer))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($session === null || $session->identity === null) {
            return null;
        }

        if ($request !== null) {
            $this->touch($session, $request);
        }

        return $session->identity;
    }

    public function revoke(string $bearer): void
    {
        FeedbackSession::query()
            ->where('token_hash', hash('sha256', $bearer))
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now(), 'updated_at' => now()]);
    }

    public function touch(FeedbackSession $session, Request $request): void
    {
        $lastSeen = $session->last_seen_at;
        $updates = [
            'last_seen_at' => now(),
            'last_seen_ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ];

        if ($lastSeen === null || $lastSeen->lt(now()->subDay())) {
            $updates['expires_at'] = now()->addDays(30);
        }

        $session->forceFill($updates)->save();
        $session->identity?->forceFill([
            'last_seen_at' => now(),
            'last_seen_ip' => $request->ip(),
        ])->save();
    }
}
