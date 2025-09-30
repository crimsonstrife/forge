<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReverbHealthCheck extends Command
{
    protected $signature = 'reverb:health';
    protected $description = 'Check that the Reverb server is reachable (TCP)';

    public function handle(): int
    {
        $host = env('REVERB_HOST', '127.0.0.1');
        $port = (int) env('REVERB_PORT', 8080);

        $errno = 0; $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, 2.0);

        if ($fp === false) {
            logger()->error('Reverb health check failed', compact('host','port','errno','errstr'));
            $this->error("Reverb unreachable at {$host}:{$port} ({$errno}) {$errstr}");
            return 1;
        }

        fclose($fp);
        $this->info("Reverb OK at {$host}:{$port}");
        return 0;
    }
}
