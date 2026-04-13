<?php

namespace App\Services\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketKeyService
{
    public function __construct(private ?string $prefix = null)
    {
        $this->prefix = $this->prefix ?: (string) config('support.ticket_key_prefix', 'SD');
    }

    public function nextKey(): string
    {
        $prefix = Str::upper((string) $this->prefix);

        $counter = DB::transaction(function () use ($prefix): int {
            $sequence = DB::table('ticket_key_sequences')
                ->where('prefix', $prefix)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                DB::table('ticket_key_sequences')->insertOrIgnore([
                    'prefix' => $prefix,
                    'current_value' => $this->currentMaxTicketNumber($prefix),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sequence = DB::table('ticket_key_sequences')
                    ->where('prefix', $prefix)
                    ->lockForUpdate()
                    ->first();
            }

            $nextValue = ((int) ($sequence->current_value ?? 0)) + 1;

            DB::table('ticket_key_sequences')
                ->where('prefix', $prefix)
                ->update([
                    'current_value' => $nextValue,
                    'updated_at' => now(),
                ]);

            return $nextValue;
        }, 5);

        return sprintf('%s-%d', $prefix, $counter);
    }

    private function currentMaxTicketNumber(string $prefix): int
    {
        $pattern = sprintf('/^%s-(\d+)$/', preg_quote($prefix, '/'));

        return DB::table('tickets')
            ->where('key', 'like', $prefix.'-%')
            ->pluck('key')
            ->reduce(static function (int $max, string $key) use ($pattern): int {
                if (! preg_match($pattern, $key, $matches)) {
                    return $max;
                }

                return max($max, (int) $matches[1]);
            }, 0);
    }
}
