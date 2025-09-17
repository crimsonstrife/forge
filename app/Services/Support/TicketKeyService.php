<?php

namespace App\Services\Support;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TicketKeyService
{
    public function __construct(private ?string $prefix = null)
    {
        $this->prefix = $this->prefix ?: (string) config('support.ticket_key_prefix', 'SD');
    }

    /**
     * @throws LockTimeoutException
     */
    public function nextKey(): string
    {
        $counter = Cache::lock('support:ticket_key_lock', 5)->block(5, function () {
            $seq = (int) Cache::get('support:ticket_key_seq', 1000);
            $seq++;
            Cache::forever('support:ticket_key_seq', $seq);
            return $seq;
        });

        return sprintf('%s-%d', Str::upper($this->prefix), $counter);
    }
}
