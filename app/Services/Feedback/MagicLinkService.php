<?php

namespace App\Services\Feedback;

use App\Mail\FeedbackMagicLinkMail;
use App\Models\FeedbackBoard;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackMagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MagicLinkService
{
    public function requestForEmail(string $email, FeedbackBoard $board, Request $request): void
    {
        $normalized = Str::lower(trim($email));
        $emailHash = FeedbackIdentity::emailHash($normalized);
        $token = Str::random(40);

        DB::transaction(static function () use ($normalized, $emailHash, $token, $board, $request): void {
            /** @var FeedbackIdentity $identity */
            $identity = FeedbackIdentity::query()->firstOrCreate(
                ['email_hash' => $emailHash],
                [
                    'email_encrypted' => $normalized,
                    'last_seen_ip' => $request->ip(),
                    'last_seen_at' => now(),
                ]
            );

            /** @var FeedbackMagicLink $magicLink */
            $magicLink = FeedbackMagicLink::query()->create([
                'email_hash' => $emailHash,
                'identity_id' => $identity->getKey(),
                'service_product_id' => $board->service_product_id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addMinutes(15),
                'request_ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);

            Mail::to($normalized)->queue(new FeedbackMagicLinkMail($board, $magicLink, $token));
        });
    }

    public function verifyToken(string $token, ?string $displayName = null): MagicLinkVerifyResult
    {
        /** @var FeedbackMagicLink|null $magicLink */
        $magicLink = FeedbackMagicLink::query()
            ->with('identity')
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if ($magicLink === null || $magicLink->consumed_at !== null || $magicLink->expires_at?->isPast()) {
            throw ValidationException::withMessages([
                'token' => __('This magic link is invalid or expired.'),
            ]);
        }

        /** @var FeedbackIdentity $identity */
        $identity = $magicLink->identity;
        $requiresDisplayName = blank($identity->display_name) && blank($displayName);

        if ($requiresDisplayName) {
            return new MagicLinkVerifyResult($identity, true, $magicLink);
        }

        DB::transaction(static function () use ($identity, $magicLink, $displayName): void {
            $identity->forceFill([
                'display_name' => $displayName ?: $identity->display_name,
                'email_verified_at' => $identity->email_verified_at ?? now(),
                'last_seen_at' => now(),
            ])->save();

            $magicLink->forceFill([
                'identity_id' => $identity->getKey(),
                'consumed_at' => now(),
            ])->save();
        });

        return new MagicLinkVerifyResult($identity->fresh(), false, $magicLink->fresh());
    }
}
