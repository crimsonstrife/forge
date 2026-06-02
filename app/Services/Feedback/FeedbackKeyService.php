<?php

namespace App\Services\Feedback;

use App\Models\FeedbackBoard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeedbackKeyService
{
    public function nextKey(FeedbackBoard $board): string
    {
        $prefix = $this->prefixFor($board);

        $counter = DB::transaction(function () use ($prefix): int {
            $row = DB::table('feedback_key_counters')
                ->where('prefix', $prefix)
                ->lockForUpdate()
                ->first();

            if ($row === null) {
                DB::table('feedback_key_counters')->insertOrIgnore([
                    'prefix' => $prefix,
                    'current_value' => $this->currentMaxPostNumber($prefix),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $row = DB::table('feedback_key_counters')
                    ->where('prefix', $prefix)
                    ->lockForUpdate()
                    ->first();
            }

            $next = ((int) ($row->current_value ?? 0)) + 1;

            DB::table('feedback_key_counters')
                ->where('prefix', $prefix)
                ->update([
                    'current_value' => $next,
                    'updated_at' => now(),
                ]);

            return $next;
        }, 5);

        return sprintf('%s-F-%d', $prefix, $counter);
    }

    private function prefixFor(FeedbackBoard $board): string
    {
        $product = $board->relationLoaded('product') ? $board->product : $board->product()->first();
        $prefix = $product?->key_prefix ?? $product?->key ?? $board->slug;

        return Str::upper(Str::of((string) $prefix)->slug('')->substr(0, 12)->toString() ?: 'FB');
    }

    private function currentMaxPostNumber(string $prefix): int
    {
        $keys = DB::table('feedback_posts')
            ->where('key', 'like', $prefix.'-F-%')
            ->latest('created_at')
            ->limit(500)
            ->pluck('key');

        return $keys->reduce(static function (int $max, string $key): int {
            if (! preg_match('/-F-(\d+)$/', $key, $matches)) {
                return $max;
            }

            return max($max, (int) $matches[1]);
        }, 0);
    }
}
